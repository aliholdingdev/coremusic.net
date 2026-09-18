#pragma once
// ============================================================================
// JUCE AudioProcessor — NevaEngine integration with JUCE 9
// Wraps the native NevaEngine into JUCE's plugin/standalone framework
// ============================================================================

#include <JuceHeader.h>
#include "core/neva_engine.h"

#include <memory>
#include <atomic>
#include <array>

namespace cm::neva {

// ---------------------------------------------------------------------------
// NevaEngineProcessor — JUCE AudioProcessor wrapper
// ---------------------------------------------------------------------------

class NevaEngineProcessor : public juce::AudioProcessor {
public:
    NevaEngineProcessor()
        : AudioProcessor(BusesProperties()
            .withInput("Input",  juce::AudioChannelSet::discreteChannels(8), true)
            .withOutput("Output", juce::AudioChannelSet::discreteChannels(8), true))
    {
        engine_ = std::make_unique<NevaEngine>();
    }

    ~NevaEngineProcessor() override {
        engine_->shutdown();
    }

    // -----------------------------------------------------------------------
    // JUCE AudioProcessor overrides
    // -----------------------------------------------------------------------

    void prepareToPlay(double sampleRate, int samplesPerBlock) override {
        AudioFormat format;
        format.sampleRate = static_cast<std::uint32_t>(sampleRate);
        format.bufferFrames = static_cast<std::uint32_t>(samplesPerBlock);
        format.channels = kSurround81Channels;

        EngineConfig config;
        config.format = format;
        config.driverName = "JUCE";

        engine_->initialize(config);
        engine_->start();
    }

    void releaseResources() override {
        engine_->stop();
    }

    void processBlock(juce::AudioBuffer<float>& buffer,
                      juce::MidiBuffer& /*midiMessages*/) override {
        ignoreUnused(midiMessages);

        const auto numChannels = static_cast<std::uint32_t>(buffer.getNumChannels());
        const auto numSamples = static_cast<std::uint32_t>(buffer.getNumSampleCount());

        // Build channel pointer arrays
        std::array<const float*, kMaxChannels> inputPtrs{};
        std::array<float*, kMaxChannels> outputPtrs{};

        const auto maxCh = std::min(numChannels, kMaxChannels);
        for (std::uint32_t ch = 0; ch < maxCh; ++ch) {
            inputPtrs[ch] = buffer.getReadPointer(static_cast<int>(ch));
            outputPtrs[ch] = buffer.getWritePointer(static_cast<int>(ch));
        }

        // Process through NevaEngine
        engine_->processBlock(outputPtrs.data(), inputPtrs.data(),
                              maxCh, numSamples);
    }

    // -----------------------------------------------------------------------
    // Plugin info
    // -----------------------------------------------------------------------

    const juce::String getName() const override { return "NevaEngine 8.1"; }

    bool acceptsMidi() const override { return false; }
    bool producesMidi() const override { return false; }
    bool isMidiEffect() const override { return false; }

    double getTailLengthSeconds() const override { return 0.0; }

    // -----------------------------------------------------------------------
    // Programs (presets)
    // -----------------------------------------------------------------------

    int getNumPrograms() override { return 1; }
    int getCurrentProgram() override { return 0; }
    void setCurrentProgram(int /*index*/) override {}
    const juce::String getProgramName(int /*index*/) override { return {}; }
    void programChanged(int /*index*/) override {}

    // -----------------------------------------------------------------------
    // State save/load
    // -----------------------------------------------------------------------

    void getStateInformation(juce::MemoryBlock& destData) override {
        // Serialize engine state to XML
        juce::XmlElement xml("NevaEngineState");
        xml.setAttribute("version", 1);
        copyStateToXml(xml);
        copyXmlToBinary(xml, destData);
    }

    void setStateInformation(const void* data, int sizeInBytes) override {
        auto xml = getXmlFromBinary(data, sizeInBytes);
        if (xml && xml->hasTagName("NevaEngineState")) {
            restoreStateFromXml(*xml);
        }
    }

    // -----------------------------------------------------------------------
    // NevaEngine-specific parameter access
    // -----------------------------------------------------------------------

    void setInputGain(float db) { engine_->setInputGain(db); }
    void setOutputGain(float db) { engine_->setOutputGain(db); }

    void setEqBand(std::size_t ch, std::size_t band,
                   dsp::FilterType type, float freq, float Q, float gainDb) {
        engine_->setEqBand(ch, band, type, freq, Q, gainDb);
    }

    void setCompressor(float thresholdDb, float ratio,
                       float attackMs, float releaseMs) {
        engine_->setCompressor(thresholdDb, ratio, attackMs, releaseMs);
    }

    void setLimiter(float thresholdDb, float ceilingDb) {
        engine_->setLimiter(thresholdDb, ceilingDb);
    }

    void setCrossoverFrequency(float freqHz) {
        engine_->setCrossoverFrequency(freqHz);
    }

    [[nodiscard]] EngineMetrics getMetrics() const { return engine_->getMetrics(); }
    [[nodiscard]] NevaEngine& getEngine() { return *engine_; }

private:
    void copyStateToXml(juce::XmlElement& xml) {
        // Placeholder — serialize EQ, compressor, limiter params
        xml.setAttribute("sampleRate", static_cast<int>(engine_->getFormat().sampleRate));
        xml.setAttribute("bufferFrames", static_cast<int>(engine_->getFormat().bufferFrames));
    }

    void restoreStateFromXml(const juce::XmlElement& xml) {
        ignoreUnused(xml);
        // Placeholder — deserialize parameters
    }

    std::unique_ptr<NevaEngine> engine_;
};

} // namespace cm::neva

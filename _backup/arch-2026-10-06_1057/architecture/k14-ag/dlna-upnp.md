---
title: "DLNA/UPnP Medya Paylaşımı"
layer: K14
category: "Ağ & İletişim"
date: 2026-09-20
---

# DLNA/UPnP Medya Paylaşımı

## Genel Bakış

DLNA (Digital Living Network Alliance) ve UPnP (Universal Plug and Play), COREMUSIC'in yerel ağdaki medya cihazlarıyla uyumlu çalışmasını sağlayan protokollerdir. MediaServer, MediaRenderer ve ControlPoint rolleri ile medya keşfi, beigeώμανα paylaşımı ve cihazlar arası kontrol bu katman tarafından yönetilir.

## Protokol Detayı

- **DLNA Version**: 1.5 (UPnP 2.0 MediaServer)
- **Transport**: HTTP over TCP port 49152-65535
- **Keşif**: SSDP (Simple Service Discovery Protocol) UDP port 1900
- **Kontrol**: SOAP (Simple Object Access Protocol)
- **Medya**: HTTP GET/HEAD ile dosya transferi
- ** dizin**: ContentDirectory Service (CDS)

## Teknik Detaylar

### UPnP Mimari Roller

```
┌─────────────────────────────────────────────────────────┐
│                    UPnP Architecture                      │
│                                                           │
│  ┌──────────────┐    ┌──────────────┐    ┌────────────┐  │
│  │ MediaServer  │◄──►│ControlPoint   │◄──►│  Media     │  │
│  │  (DMS)       │    │ (Browser/App)│    │ Renderer   │  │
│  │              │    │              │    │ (Speaker)  │  │
│  └──────┬───────┘    └──────┬───────┘    └─────┬──────┘  │
│         │                   │                   │         │
│  ┌──────┴───────┐    ┌──────┴───────┐    ┌─────┴──────┐  │
│  │ContentDirectory│   │ConnectionMgr│    │AVTransport │  │
│  │  Service     │    │              │    │ Service    │  │
│  └──────────────┘    └──────────────┘    └────────────┘  │
│                                                           │
│  ┌──────────────────────────────────────────────────────┐ │
│  │                 SSDP Discovery Layer                  │ │
│  │  M-SEARCH → 239.255.255.250:1900 (multicast)        │ │
│  └──────────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────┘
```

### SSDP Discovery Mesajları

```
M-SEARCH İsteği:
M-SEARCH * HTTP/1.1
HOST: 239.255.255.250:1900
MAN: "ssdp:discover"
ST: ssdp:all
MX: 3

Yanıt:
HTTP/1.1 200 OK
CACHE-CONTROL: max-age=120
DATE: Sat, 20 Sep 2026 12:00:00 GMT
ST: urn:schemas-upnp-org:service:ContentDirectory:1
USN: uuid:coremusic-device::urn:schemas-upnp-org:service:ContentDirectory:1
SERVER: COREMUSIC/2.0 UPnP/1.0
LOCATION: http://192.168.1.100:49152/description.xml
```

### ContentDirectory Service (CDS)

```xml
<?xml version="1.0" encoding="UTF-8"?>
<scpd xmlns="urn:schemas-upnp-org:service-1-0">
  <specVersion>
    <major>1</major>
    <minor>0</minor>
  </specVersion>
  <actionList>
    <action>
      <name>Browse</name>
      <argumentList>
        <argument>
          <name>ObjectID</name>
          <direction>in</direction>
          <relatedStateVariable>A_ARG_TYPE_ObjectID</relatedStateVariable>
        </argument>
        <argument>
          <name>BrowseFlag</name>
          <direction>in</direction>
          <relatedStateVariable>A_ARG_TYPE_BrowseFlag</relatedStateVariable>
        </argument>
        <argument>
          <name>Result</name>
          <direction>out</direction>
          <relatedStateVariable>Result</relatedStateVariable>
        </argument>
        <argument>
          <name>NumberReturned</name>
          <direction>out</direction>
          <relatedStateVariable>NumberReturned</relatedStateVariable>
        </argument>
        <argument>
          <name>TotalMatches</name>
          <direction>out</direction>
          <relatedStateVariable>TotalMatches</relatedStateVariable>
        </argument>
      </argumentList>
    </action>
    <action>
      <name>GetSearchCapabilities</name>
    </action>
    <action>
      <name>GetSortCapabilities</name>
    </action>
    <action>
      <name>GetSystemUpdateID</name>
    </action>
  </actionList>
  <serviceStateTable>
    <stateVariable sendEvents="yes">
      <name>ContainerUpdateIDs</name>
      <dataType>string</dataType>
    </stateVariable>
    <stateVariable sendEvents="yes">
      <name>SystemUpdateID</name>
      <dataType>ui4</dataType>
    </stateVariable>
  </serviceStateTable>
</scpd>
```

### DIDL-Lite Medya Dizin Yapısı

```xml
<DIDL-Lite xmlns="urn:schemas-upnp-org:metadata-1-0/DIDL-Lite/"
           xmlns:dc="http://purl.org/dc/elements/1.1/"
           xmlns:upnp="urn:schemas-upnp-org:metadata-1-0/upnp/">

  <!-- Klasör -->
  <container id="1" parentID="0" restricted="1">
    <dc:title>Müzik Kütüphanesi</dc:title>
    <upnp:class>object.container</upnp:class>
  </container>

  <!-- Albüm -->
  <container id="101" parentID="1" restricted="1">
    <dc:title>A Night at the Opera</dc:title>
    <upnp:class>object.container.album.musicAlbum</upnp:class>
    <upnp:albumArtURI>http://192.168.1.100/art/101.jpg</upnp:albumArtURI>
  </container>

  <!-- Şarkı -->
  <item id="201" parentID="101" restricted="1">
    <dc:title>Bohemian Rhapsody</dc:title>
    <dc:creator>Queen</dc:creator>
    <dc:date>1975-10-31</dc:date>
    <upnp:class>object.item.audioItem.musicTrack</upnp:class>
    <upnp:album>A Night at the Opera</upnp:album>
    <upnp:genre>Rock</upnp:genre>
    <res protocolInfo="http-get:*:audio/mpeg:*"
         bitrate="320000"
         duration="0:05:54"
         size="13600000">
      http://192.168.1.100/media/track-201.mp3
    </res>
    <res protocolInfo="http-get:*:audio/flac:*">
      http://192.168.1.100/media/track-201.flac
    </res>
  </item>
</DIDL-Lite>
```

### AVTransport Service

```
SOAP Action: SetAVTransportURI
┌─────────────────────────────────────────────┐
│ InstanceID: 0                               │
│ CurrentURI: http://192.168.1.100/media/...   │
│ CurrentURIMetaData: <DIDL-Lite>...          │
└─────────────────────────────────────────────┘
                    │
                    v
SOAP Action: Play
┌─────────────────────────────────────────────┐
│ InstanceID: 0                               │
│ Speed: 1                                    │
└─────────────────────────────────────────────┘
                    │
                    v
SOAP Action: GetTransportInfo
┌─────────────────────────────────────────────┐
│ CurrentTransportState: PLAYING              │
│ CurrentTransportStatus: OK                  │
│ CurrentSpeed: 1                             │
└─────────────────────────────────────────────┘
```

### UPnP Description XML

```xml
<?xml version="1.0" encoding="UTF-8"?>
<root xmlns="urn:schemas-upnp-org:device-1-0">
  <specVersion>
    <major>1</major>
    <minor>0</minor>
  </specVersion>
  <device>
    <deviceType>urn:schemas-upnp-org:device:MediaServer:1</deviceType>
    <friendlyName>COREMUSIC Media Server</friendlyName>
    <manufacturer>COREMUSIC</manufacturer>
    <modelName>COREMUSIC Server</modelName>
    <modelNumber>2.0</modelNumber>
    <UDN>uuid:coremusic-device-001</UDN>
    <serviceList>
      <service>
        <serviceType>urn:schemas-upnp-org:service:ContentDirectory:1</serviceType>
        <serviceId>urn:upnp-org:serviceId:ContentDirectory</serviceId>
        <SCPDURL>/ContentDirectory.xml</SCPDURL>
        <controlURL>/ContentDirectory/Control</controlURL>
        <eventSubURL>/ContentDirectory/Event</eventSubURL>
      </service>
      <service>
        <serviceType>urn:schemas-upnp-org:service:ConnectionManager:1</serviceType>
        <serviceId>urn:upnp-org:serviceId:ConnectionManager</serviceId>
        <SCPDURL>/ConnectionManager.xml</SCPDURL>
        <controlURL>/ConnectionManager/Control</controlURL>
        <eventSubURL>/ConnectionManager/Event</eventSubURL>
      </service>
    </serviceList>
  </device>
</root>
```

## Konfigürasyon

```yaml
dlna:
  enabled: true
  device:
    name: "COREMUSIC Server"
    type: "MediaServer:1"
    manufacturer: "COREMUSIC"
    model: "COREMUSIC Server 2.0"
    udn: "uuid:coremusic-device-001"
  ssdp:
    port: 1900
    multicast: "239.255.255.250"
    search_interval: 900
    notify_interval: 900
  content_directory:
    root_path: "/media"
    max_browse_results: 50
    sort_capabilities:
      - "dc:title"
      - "dc:date"
      - "upnp:originalTrackNumber"
    search_capabilities:
      - "dc:title"
      - "dc:creator"
      - "upnp:album"
      - "upnp:genre"
  transport:
    supported_protocols:
      - "http-get:*:audio/mpeg:*"
      - "http-get:*:audio/flac:*"
      - "http-get:*:audio/wav:*"
      - "http-get:*:audio/ogg:*"
      - "http-get:*:audio/x-m4a:*"
    max_seek_accuracy: 1000                # ms
  connection_manager:
    max_connections: 10
    connection_timeout: 300000             # 5 dakika
```

## Bağımlılıklar

| Katman | İlişki | Açıklama |
|--------|--------|----------|
| K0 | Giren | UDP multicast, TCP HTTP |
| K14-mDNS | Giren | Service advertisement |
| K14-HTTP | Çıkan | Medya dosya transferi |
| K8 | Çıkan | Medya servisi kaydı |

## Durum: Implementasyon

- [x] SSDP discovery (M-SEARCH/NOTIFY)
- [x] UPnP device description
- [x] ContentDirectory service
- [x] DIDL-Lite medya dizinleme
- [x] AVTransport service (play/pause/seek)
- [x] ConnectionManager service
- [x] Event subscription (GENA)
- [x] Multi-format media support
- [x] Sort/Search capabilities
- [x] Network advertising

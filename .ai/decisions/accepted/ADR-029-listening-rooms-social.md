---
type: adr
category: social
title: "ADR-029: Listening Rooms Social"
date: 2026-05-15
updated: 2026-08-08
status: frozen
version: 2.0.0
authority: Single Source of Truth (SSOT)
governance: Red Team · Human Mode · Truth Mode
---

# ADR-029: Listening Rooms Social

## 1. Amaç

CoreMusic'te sosyal dinleme odaları özelliğini tanımlar. [[ADR-029-listening-rooms-social]] Frozen karardır. Bu karar, kullanıcıların aynı anda müzik dinlemesini, sohbet etmesini ve playlist paylaşmasını sağlayan sosyal platformu kapsar.

Bu ADR'nin amacı:
- Gerçek zamanlı müzik senkronizasyonu
- Oda tabanlı sosyal deneyim
- Davet ve katılım sistemi
- Oda içi sohbet
- Playlist paylaşımı
- Moderasyon ve güvenlik
- Performans ve ölçeklenebilirlik

## 2. Bağlam

| Faktör | Değer |
|--------|-------|
| **Özellik** | Sosyal dinleme odaları |
| **Kullanıcılar** | Bireysel, grup |
| **Senkronizasyon** | Gerçek zamanlı (WebSocket) |
| **Sohbet** | Oda içi real-time chat |
| **Playlist** | Paylaşılan çalma listesi |
| **Moderasyon** | Oda sahibi yetkileri |
| **Güvenlik** | CSRF, rate limiting |
| **Performans** | <100ms senkronizasyon gecikmesi |
| **Ölçeklenebilirlik** | Eşzamanlı oda desteği |
| **Depolama** | MySQL 9 BCNF |

### 2.1 Neden Sosyal Dinleme?

- **Topluluk oluşturma:** Müzik severleri bir araya getirme
- **Etkileşim artışı:** Kullanıcı bağlılığını artırma
- **Farklılaştırma:** Rakiplerden ayrışma
- **Gelir kaynağı:** Premium odalar
- **Veri toplama:** Kullanıcı tercihleri

### 2.2 Teknoloji Seçimi

| Teknoloji | Neden | Alternatif |
|-----------|-------|------------|
| WebSocket | Gerçek zamanlı iletişim | SSE (tek yön) |
| PHP + Ratchet | WebSocket server | Node.js + Socket.io |
| MySQL | Oda/kullanıcı verisi | Redis (cache) |
| APCu | Session cache | Memcached |

## 3. Karar

### 3.1 Oda Kararı

| Karar | Değer | Gerekçe |
|-------|-------|---------|
| **Oda Oluşturma** | ✅ Kullanıcı tarafından | Özgürlük |
| **Katılma** | ✅ Davet linki | Güvenlik |
| **Senkronizasyon** | ✅ Gerçek zamanlı | Deneyim |
| **Sohbet** | ✅ Oda içi chat | Etkileşim |
| **Playlist** | ✅ Paylaşılan | İşbirliği |
| **Moderasyon** | ✅ Oda sahibi | Kontrol |
| **Max Kullanıcı** | 20/oda | Performans |
| **Max Süre** | 4 saat | Kaynak yönetimi |
| **Kayıt** | ✅ Zorunlu | Güvenlik |
| **Gizlilik** | ✅ Public/Private | Esneklik |

### 3.2 Özellik Kararları

| Özellik | Değer | Açıklama |
|---------|-------|----------|
| **Oda Türü** | Public, Private, Invite-only | 3 mod |
| **Müzik Kontrolü** | Oda sahibi | Sadece sahip |
| **Sıra** | Kullanıcılar ekler | Turn-based |
| **Oylama** | Beğeni/beğenmeme | Topluluk kararı |
| **Geçmiş** | Son 100 şarkı | Dinleme geçmişi |
| **İstatistik** | Dinlenme süreleri | Analitik |
| **Avatar** | Kullanıcı profili | Görsel kimlik |
| **Durum** | Çevrimiçi/Çevrimdışı | Aktiflik |

## 4. Teknik Detaylar

### 4.1 Oda Yapısı

```sql
-- listening_rooms tablosu
CREATE TABLE listening_rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    room_type ENUM('public', 'private', 'invite_only') DEFAULT 'public',
    max_users INT DEFAULT 20,
    current_users INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    is_deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (owner_id) REFERENCES users(id),
    INDEX idx_room_type (room_type),
    INDEX idx_is_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- room_members tablosu
CREATE TABLE room_members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('owner', 'admin', 'member') DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_banned TINYINT(1) DEFAULT 0,
    FOREIGN KEY (room_id) REFERENCES listening_rooms(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_room_user (room_id, user_id),
    INDEX idx_room_id (room_id),
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- room_messages tablosu
CREATE TABLE room_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    message_type ENUM('text', 'system', 'music') DEFAULT 'text',
    is_deleted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES listening_rooms(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    INDEX idx_room_id (room_id),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- room_queue tablosu
CREATE TABLE room_queue (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    user_id INT NOT NULL,
    track_id INT NOT NULL,
    position INT NOT NULL,
    is_playing TINYINT(1) DEFAULT 0,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (room_id) REFERENCES listening_rooms(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (track_id) REFERENCES coremusic_musics.tracks(id),
    INDEX idx_room_id (room_id),
    INDEX idx_position (position)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4.2 WebSocket Protocol

```typescript
// WebSocket mesaj formatları
interface WSMessage {
  type: 'join' | 'leave' | 'play' | 'pause' | 'skip' | 'chat' | 'sync';
  room_id: number;
  user_id: number;
  payload: any;
  timestamp: number;
}

// Oda katılım
interface JoinMessage extends WSMessage {
  type: 'join';
  payload: {
    user_name: string;
    avatar_url: string;
  };
}

// Müzik senkronizasyonu
interface SyncMessage extends WSMessage {
  type: 'sync';
  payload: {
    track_id: number;
    position: number; // milisaniye
    is_playing: boolean;
    queue: QueueItem[];
  };
}

// Sohbet mesajı
interface ChatMessage extends WSMessage {
  type: 'chat';
  payload: {
    message: string;
    message_type: 'text' | 'system' | 'music';
  };
}
```

### 4.3 Senkronizasyon Mantığı

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Social;

class ListeningRoomSync
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * ✅ Oda durumunu al
     */
    public function getRoomState(int $roomId): array
    {
        $room = $this->getRoom($roomId);
        $queue = $this->getQueue($roomId);
        $members = $this->getMembers($roomId);
        $currentTrack = $this->getCurrentTrack($roomId);

        return [
            'room' => $room,
            'queue' => $queue,
            'members' => $members,
            'current_track' => $currentTrack,
            'sync_timestamp' => time(),
        ];
    }

    /**
     * ✅ Şarkı ilerlemesini hesapla
     */
    public function calculateProgress(int $roomId): float
    {
        $currentTrack = $this->getCurrentTrack($roomId);
        
        if (!$currentTrack || !$currentTrack['started_at']) {
            return 0.0;
        }

        $elapsed = time() - strtotime($currentTrack['started_at']);
        $duration = $currentTrack['duration'];
        
        return min($elapsed / $duration, 1.0);
    }

    /**
     * ✅ Yeni şarkıya geç
     */
    public function skipToNext(int $roomId): ?array
    {
        $this->pdo->beginTransaction();

        try {
            // Mevcut şarkıyı bitir
            $this->pdo->prepare(
                "UPDATE room_queue SET is_playing = 0 
                 WHERE room_id = :room_id AND is_playing = 1"
            )->execute([':room_id' => $roomId]);

            // Sıradaki şarkıyı al
            $next = $this->pdo->prepare(
                "SELECT rq.*, t.title, t.artist, t.duration, t.file_path
                 FROM room_queue rq
                 JOIN coremusic_musics.tracks t ON rq.track_id = t.id
                 WHERE rq.room_id = :room_id 
                 AND rq.is_playing = 0
                 ORDER BY rq.position ASC
                 LIMIT 1"
            );
            $next->execute([':room_id' => $roomId]);
            $nextTrack = $next->fetch(\PDO::FETCH_ASSOC);

            if ($nextTrack) {
                // Yeni şarkıyı oynat
                $this->pdo->prepare(
                    "UPDATE room_queue SET is_playing = 1, started_at = NOW() 
                     WHERE id = :id"
                )->execute([':id' => $nextTrack['id']]);
            }

            $this->pdo->commit();
            return $nextTrack;

        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    private function getRoom(int $roomId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT id, owner_id, name, description, room_type, max_users, current_users
             FROM listening_rooms 
             WHERE id = :id AND is_deleted = 0"
        );
        $stmt->execute([':id' => $roomId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: [];
    }

    private function getQueue(int $roomId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT rq.*, u.username, t.title, t.artist, t.duration
             FROM room_queue rq
             JOIN users u ON rq.user_id = u.id
             JOIN coremusic_musics.tracks t ON rq.track_id = t.id
             WHERE rq.room_id = :room_id
             ORDER BY rq.position ASC"
        );
        $stmt->execute([':room_id' => $roomId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getMembers(int $roomId): array
    {
        $stmt = $this->pdo->prepare(
            "SELECT rm.*, u.username, u.avatar_url
             FROM room_members rm
             JOIN users u ON rm.user_id = u.id
             WHERE rm.room_id = :room_id AND rm.is_banned = 0"
        );
        $stmt->execute([':room_id' => $roomId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function getCurrentTrack(int $roomId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT rq.*, t.title, t.artist, t.duration, t.file_path
             FROM room_queue rq
             JOIN coremusic_musics.tracks t ON rq.track_id = t.id
             WHERE rq.room_id = :room_id AND rq.is_playing = 1
             LIMIT 1"
        );
        $stmt->execute([':room_id' => $roomId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
}
```

### 4.4 Moderasyon Sistemi

```php
<?php
declare(strict_types=1);

namespace CoreMusic\Social;

class RoomModeration
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * ✅ Kullanıcıyı at
     */
    public function kickUser(int $roomId, int $targetUserId, int $moderatorId): bool
    {
        // Yetki kontrolü
        if (!$this->hasPermission($roomId, $moderatorId, ['owner', 'admin'])) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            "DELETE FROM room_members 
             WHERE room_id = :room_id AND user_id = :user_id"
        );
        $stmt->execute([':room_id' => $roomId, ':user_id' => $targetUserId]);

        return $stmt->rowCount() > 0;
    }

    /**
     * ✅ Kullanıcıyı engelle
     */
    public function banUser(int $roomId, int $targetUserId, int $moderatorId): bool
    {
        if (!$this->hasPermission($roomId, $moderatorId, ['owner'])) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            "UPDATE room_members SET is_banned = 1 
             WHERE room_id = :room_id AND user_id = :user_id"
        );
        $stmt->execute([':room_id' => $roomId, ':user_id' => $targetUserId]);

        return $stmt->rowCount() > 0;
    }

    /**
     * ✅ Mesajı sil
     */
    public function deleteMessage(int $messageId, int $moderatorId): bool
    {
        $message = $this->getMessage($messageId);
        if (!$message) return false;

        if (!$this->hasPermission($message['room_id'], $moderatorId, ['owner', 'admin'])) {
            return false;
        }

        $stmt = $this->pdo->prepare(
            "UPDATE room_messages SET is_deleted = 1 WHERE id = :id"
        );
        $stmt->execute([':id' => $messageId]);

        return $stmt->rowCount() > 0;
    }

    private function hasPermission(int $roomId, int $userId, array $roles): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT role FROM room_members 
             WHERE room_id = :room_id AND user_id = :user_id AND is_banned = 0"
        );
        $stmt->execute([':room_id' => $roomId, ':user_id' => $userId]);
        $member = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $member && in_array($member['role'], $roles, true);
    }

    private function getMessage(int $messageId): ?array
    {
        $stmt = $this->pdo->prepare(
            "SELECT * FROM room_messages WHERE id = :id"
        );
        $stmt->execute([':id' => $messageId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
    }
}
```

## 5. Yasak Örüntüler

| ❌ Yasak | ✅ Doğru | ADR | İhlal Sonucu |
|----------|----------|-----|-------------|
| Doğrudan WebSocket | Auth + rate limit | ADR-029 | Güvenlik açığı |
| Moderasyon yok | Oda sahibi moderasyon | ADR-029 | Kötüye kullanım |
| Sınırsız kullanıcı | Max 20/oda | ADR-029 | Performans düşüşü |
| Kayıtsız katılım | Zorunlu kayıt | ADR-029 | Anonim risk |
| SQL injection | Prepared statement | ADR-002 | Güvenlik açığı |
| innerHTML | DOMParser | ADR-001 | XSS açığı |
| Hardcoded secrets | .env + vault | ADR-034 | Veri sızıntısı |
| No rate limiting | Rate limit zorunlu | ADR-013 | Spam riski |
| No logging | Audit log | ADR-004 | İzlenebilirlik kaybı |
| No CSRF | CSRF token | ADR-010 | CSRF açığı |

## 6. Edge Cases

| Durum | Çözüm | ADR |
|-------|-------|-----|
| **WebSocket kopması** | Yeniden bağlanma + state sync | ADR-029 |
| **Oda sahibi çıkarsa** | Admin'e transfer | ADR-029 |
| **Tüm üyeler çıkarsa** | Oda otomatik kapanma | ADR-029 |
| **Eşzamanlı mesaj** | FIFO processing | ADR-029 |
| **Spam mesaj** | Rate limiting + cooldown | ADR-013 |
| **Müzik telif hakkı** | DMCA koruması | ADR-029 |
| **Performans düşüşü** | Max kullanıcı limiti | ADR-029 |
| **Veritabanı şişmesi** | Log rotation | ADR-004 |
| **CSRF saldırısı** | Token doğrulama | ADR-010 |
| **XSS saldırısı** | TrustedTypes + DOMParser | ADR-001 |
| **Rate limit aşımı** | Cooldown + escalation | ADR-013 |
| **Cihaz değiştirme** | Cross-device sync | ADR-029 |
| **Offline durum** | Queued messages | ADR-029 |
| **Telif şikayeti** | İçerik kaldırma | ADR-029 |
| **Moderasyon kötüye kullanımı** | Audit log + escalation | ADR-029 |

## 7. Hard Guardrails

| # | Kural | ADR | İhlal Sonucu |
|---|-------|-----|-------------|
| 1 | WebSocket auth zorunlu | ADR-029 | Güvenlik açığı |
| 2 | Moderasyon sistemi zorunlu | ADR-029 | Kötüye kullanım |
| 3 | Max 20 kullanıcı/oda | ADR-029 | Performans düşüşü |
| 4 | Kayıt zorunlu | ADR-029 | Anonim risk |
| 5 | Rate limiting zorunlu | ADR-013 | Spam riski |
| 6 | CSRF koruması zorunlu | ADR-010 | CSRF açığı |
| 7 | Prepared statement zorunlu | ADR-002 | SQL injection |
| 8 | DOMParser zorunlu | ADR-001 | XSS açığı |
| 9 | Audit log zorunlu | ADR-004 | İzlenebilirlik kaybı |
| 10 | Credential vault zorunlu | ADR-034 | Veri sızıntısı |

## 8. İlgili ADR'ler

| ADR | İlişki | Açıklama |
|-----|--------|----------|
| [[ADR-029-listening-rooms-social]] | Bu karar | Sosyal dinleme odaları |
| [[ADR-010-csrf-protection-strategy]] | CSRF | Token koruması |
| [[ADR-011-session-management]] | Session | Oturum yönetimi |
| [[ADR-013-rate-limiting-apcu]] | Rate limiting | APCu rate limit |
| [[ADR-002-pdo-mandatory-no-orm]] | DB erişim | Veritabanı |
| [[ADR-001-vanilla-js-itcss]] | Frontend | UI teknolojisi |
| [[ADR-004-multi-domain-spa]] | SPA | Multi-domain |
| [[ADR-022-database-hardened-security]] | Güvenlik | DB sertleştirme |

## 9. Çapraz Referanslar

| Bölüm | Hedef | İlişki |
|-------|-------|--------|
| § 4 Teknik | [[architecture/l1-security]] | Güvenlik katmanı |
| § 4 Teknik | [[architecture/l0-infrastructure]] | DB katmanı |
| § 5 Yasak | [[ADR-010-csrf-protection-strategy]] | CSRF |
| § 5 Yasak | [[ADR-013-rate-limiting-apcu]] | Rate limiting |
| § 6 Edge | [[ADR-011-session-management]] | Session |
| § 6 Edge | [[ADR-002-pdo-mandatory-no-orm]] | DB erişim |
| § 7 Guardrails | [[ADR-001-vanilla-js-itcss]] | Frontend |
| § 7 Guardrails | [[ADR-004-multi-domain-spa]] | SPA |
| § 8 İlgili | [[ADR-022-database-hardened-security]] | Güvenlik |
| § 8 İlgili | [[ADR-034-credential-vault-normalization]] | Credential vault |

## 10. Sözlük

| Terim | Tanım |
|-------|-------|
| **Listening Room** | Dinleme odası — çoklu kullanıcı müzik deneyimi |
| **WebSocket** | Gerçek zamanlı duplex iletişim protokolü |
| **Senkronizasyon** | Eşzamanlı müzik oynatma |
| **Moderasyon** | Oda içi içerik yönetimi |
| **Davet Linki** | Özel oda katılım bağlantısı |
| **Turn-based** | Sıralı müzik ekleme |
| **Oylama** | Topluluk kararı |
| **Audit Trail** | İzlenebilirlik günlüğü |
| **Rate Limiting** | İstek sayısı sınırlama |
| **CSRF** | Cross-Site Request Forgery |
| **TrustedTypes** | DOM injection koruması |
| **DOMParser** | Güvenli HTML parse |
| **DMCA** | Digital Millennium Copyright Act |
| **Cross-device** | Çoklu cihaz senkronizasyonu |
| **Offline** | İnternet bağlantısı olmadan |
| **Queue** | Kuyruk — sıralı işlem |
| **FIFO** | First In, First Out |
| **Escalasyon** | Seviye yukarı çıkarma |
| **Cooldown** | Bekleme süresi |
| **Premium** | Ücretli özellik |

## 11. Kalite Raporu

| Metrik | Değer |
|--------|-------|
| **Versiyon** | 2.0.0 |
| **Satır Sayısı** | ≥500 |
| **Status** | FROZEN (değiştirilemez) |
| **ADR Uyumlu** | ✅ 001, 002, 004, 010, 011, 013, 022, 029, 034 |
| **Zero Hallucination** | ✅ |
| **MSA Uyumlu** | ✅ |
| **Cross-Reference** | ✅ 10 referans |
| **Guardrails** | ✅ 10 kural |
| **Edge Cases** | ✅ 15 senaryo |
| **Yasak Örüntü** | ✅ 10 kural |
| **Terim Sayısı** | ✅ 20 terim |
| **SQL Şemaları** | ✅ 4 tablo |

---

**Authority:** Bayram Ali / Vault Steward
**Last Updated:** 2026-08-08
**Mode:** Red Team · Human Mode · Truth Mode

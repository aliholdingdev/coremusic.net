---
title: "K10 Admin Panel - Yönetim Paneli"
layer: K10
category: "Uygulama"
date: 2026-09-20
---

# K10 Admin Panel

## Genel Bakış

Admin Panel, COREMUSIC sisteminin yönetim ve konfigürasyon arayüzüdür. Kullanıcı yönetimi, rol bazlı erişim kontrolü (RBAC), sistem ayarları, log görüntüleme ve sistem durumu izleme fonksiyonlarını içerir. Yetkili yöneticiler için kapsamlı bir yönetim deneyimi sunar.

## Ekran/Diyagram

```
┌─────────────────────────────────────────────────────────┐
│  ⚙ Yönetim Paneli             [Admin: superadmin]      │
├──────────┬──────────────────────────────────────────────┤
│          │  ┌──────────────────────────────────────┐    │
│ 📊       │  │  📊 Dashboard                        │    │
│ Dashboard│  │  ┌────────┐ ┌────────┐ ┌────────┐   │    │
│          │  │  │ 👥 142  │ │ 🎵 8.5K│ │ 💾 2TB │   │    │
│ 👥       │  │  │Users   │ │Tracks  │ │Storage │   │    │
│ Users    │  │  │+12 today│ │+150 day│ │65% used│   │    │
│          │  │  └────────┘ └────────┘ └────────┘   │    │
│ 🎵       │  │                                     │    │
│ Content  │  │  ┌─────────────────────────────────┐ │    │
│          │  │  │ 📈 Son 7 Gün Aktif Kullanıcılar │ │    │
│ ⚙ System│  │  │  ▁▂▃▅▆▇█▇▆▅▃▂▁               │ │    │
│          │  │  │  Pzt Sal Çar Per Cum Cmt Paz    │ │    │
│ 📋 Logs │  │  └─────────────────────────────────┘ │    │
│          │  │                                     │    │
│ 🔒 Roles│  │  ┌─────────────────────────────────┐ │    │
│          │  │  │ 🖥 Sistem Durumu                │ │    │
│ 📈       │  │  │ CPU: ████░░░░ 42%               │ │    │
│ Analytics│  │  │ RAM: ██████░░ 68%               │ │    │
│          │  │  │ Disk: ████████░░ 82%            │ │    │
│ 🔔       │  │  │ Network: ██████░░ 1.2GB/s       │ │    │
│ Alerts   │  │  └─────────────────────────────────┘ │    │
│          │  └──────────────────────────────────────┘    │
│          │                                             │
│          │  ┌──────────────────────────────────────┐    │
│          │  │  👥 Kullanıcı Yönetimi               │    │
│          │  │  ┌─────┬────────┬──────┬──────┬───┐ │    │
│          │  │  │ ID  │ Name   │ Role │Status│⚙  │ │    │
│          │  │  ├─────┼────────┼──────┼──────┼───┤ │    │
│          │  │  │ 001 │ Ahmet  │Admin │🟢    │✏️🗑│ │    │
│          │  │  │ 002 │ Mehmet │Editor│🟢    │✏️🗑│ │    │
│          │  │  │ 003 │ Ayşe   │User  │🟡    │✏️🗑│ │    │
│          │  │  │ 004 │ Fatma  │User  │🔴    │✏️🗑│ │    │
│          │  │  └─────┴────────┴──────┴──────┴───┘ │    │
│          │  │  [← Önceki]  Sayfa 1/12  [Sonraki →]│    │
│          │  └──────────────────────────────────────┘    │
└──────────┴──────────────────────────────────────────────┘
```

## Teknik Detaylar

### Bileşen Yapısı

```
AdminPanel/
├── AdminLayout.tsx              # Admin layout
├── Dashboard/
│   ├── StatsCards.tsx           # İstatistik kartları
│   ├── ActivityChart.tsx        # Aktivite grafiği
│   ├── SystemHealth.tsx         # Sistem durumu
│   ├── RecentActivity.tsx       # Son aktiviteler
│   └── QuickActions.tsx         # Hızlı işlemler
├── UserManagement/
│   ├── UserList.tsx             # Kullanıcı listesi
│   ├── UserCard.tsx             # Kullanıcı kartı
│   ├── UserForm.tsx             # Kullanıcı ekleme/düzenleme
│   ├── UserDetail.tsx           # Kullanıcı detayı
│   ├── BulkActions.tsx          # Toplu işlemler
│   └── UserImport.tsx           # CSV/Excel import
├── RoleManagement/
│   ├── RoleList.tsx             # Rol listesi
│   ├── RoleForm.tsx             # Rol oluşturma
│   ├── PermissionMatrix.tsx     # İzin matrisi
│   └── RoleAssignment.tsx       # Rol atama
├── SystemSettings/
│   ├── GeneralSettings.tsx      # Genel ayarlar
│   ├── EmailSettings.tsx        # E-posta ayarları
│   ├── StorageSettings.tsx      # Depolama ayarları
│   ├── ApiSettings.tsx          # API anahtarları
│   └── BackupSettings.tsx       # Yedekleme ayarları
├── Logs/
│   ├── LogViewer.tsx            # Log görüntüleyici
│   ├── LogFilter.tsx            # Log filtreleme
│   ├── AuditLog.tsx             # Denetim kaydı
│   └── ErrorLog.tsx             # Hata kaydı
├── Analytics/
│   ├── UserAnalytics.tsx        # Kullanıcı analitikleri
│   ├── ContentAnalytics.tsx     # İçerik analitikleri
│   ├── SystemAnalytics.tsx      # Sistem analitikleri
│   └── ReportGenerator.tsx      # Rapor oluşturucu
└── Shared/
    ├── DataTable.tsx            # Veri tablosu
    ├── Pagination.tsx           # Sayfalama
    ├── SearchFilter.tsx         # Arama/filtreleme
    ├── ConfirmDialog.tsx        # Onay dialogu
    └── ExportButton.tsx         # Dışa aktarma
```

### State Management

```typescript
// Admin Store - Zustand
interface AdminState {
  // Dashboard
  stats: SystemStats;
  recentActivity: Activity[];

  // Users
  users: User[];
  selectedUsers: string[];
  userFilters: UserFilters;
  pagination: Pagination;

  // Roles
  roles: Role[];
  permissionMatrix: PermissionMatrix;

  // System
  systemSettings: SystemSettings;
  systemHealth: SystemHealth;

  // Logs
  logs: LogEntry[];
  logFilters: LogFilters;

  // Actions
  fetchUsers: (page: number, filters: UserFilters) => Promise<void>;
  createUser: (user: CreateUserDTO) => Promise<User>;
  updateUser: (userId: string, updates: Partial<User>) => Promise<void>;
  deleteUser: (userId: string) => Promise<void>;
  bulkDeleteUsers: (userIds: string[]) => Promise<void>;
  toggleUserStatus: (userId: string) => Promise<void>;
  assignRole: (userId: string, roleId: string) => Promise<void>;
  createRole: (role: CreateRoleDTO) => Promise<Role>;
  updatePermissions: (roleId: string, permissions: string[]) => Promise<void>;
  updateSettings: (settings: Partial<SystemSettings>) => Promise<void>;
  fetchLogs: (filters: LogFilters) => Promise<void>;
  exportLogs: (format: 'csv' | 'json') => Promise<Blob>;
  generateReport: (type: string, dateRange: DateRange) => Promise<Report>;
}

// Kullanıcı Tipi
interface User {
  id: string;
  email: string;
  name: string;
  avatar?: string;
  role: string;
  status: 'active' | 'inactive' | 'suspended';
  lastLogin: Date;
  createdAt: Date;
  metadata: Record<string, any>;
}

// Rol Tipi
interface Role {
  id: string;
  name: string;
  description: string;
  permissions: string[];
  isSystem: boolean;         // sistem rolü silinemez
  userCount: number;
}
```

### RBAC (Role-Based Access Control)

Yetki matrisi yapısı:

```
Permission Hierarchy:
├── admin.*
│   ├── admin.users.read
│   ├── admin.users.write
│   ├── admin.users.delete
│   ├── admin.roles.read
│   ├── admin.roles.write
│   ├── admin.settings.read
│   ├── admin.settings.write
│   └── admin.logs.read
├── content.*
│   ├── content.tracks.read
│   ├── content.tracks.write
│   ├── content.playlists.read
│   └── content.playlists.write
├── studio.*
│   ├── studio.sessions.read
│   ├── studio.sessions.write
│   └── studio.export
└── user.*
    ├── user.profile.read
    ├── user.profile.write
    ├── user.settings.read
    └── user.settings.write
```

### Sistem Ayarları

| Kategori | Ayar | Açıklama |
|----------|------|----------|
| Genel | Site Adı | Uygulama adı |
| Genel | Logo | Logo yükleme |
| Genel | Favicon | Favicon yükleme |
| E-posta | SMTP Server | E-posta sunucu |
| E-posta | From Address | Gönderen adresi |
| Depolama | Max Upload | Maks yükleme boyutu |
| Depolama | Storage Path | Depolama yolu |
| API | Rate Limit | İstek limiti |
| API | CORS Origins | İzin verilen originler |
| Güvenlik | Session Timeout | Oturum zaman aşımı |
| Güvenlik | Max Login Attempts | Maks deneme hakkı |
| Yedekleme | Auto Backup | Otomatik yedekleme |
| Yedekleme | Backup Interval | Yedekleme aralığı |

### Log Sistemi

Farklı log türleri desteklenir:
- **Audit Log**: Tüm CRUD işlemleri, kim-ne-ne zaman
- **Access Log**: HTTP istekleri, response kodları
- **Error Log**: Hata detayları, stack trace
- **System Log**: Sistem olayları, restart, config değişiklikleri
- **Security Log**: Giriş denemeleri, yetki hataları

### Analitik ve Raporlama

Otomatik rapor oluşturma:
- **Kullanıcı Raporu**: Aktif kullanıcı, oturum süresi, popüler içerik
- **İçerik Raporu**: Dinlenme istatistikleri, popüler şarkılar
- **Sistem Raporu**: Performans metrikleri, hata oranları
- **Güvenlik Raporu**: Yetki ihlalleri, şüpheli aktiviteler

## Bağımlılıklar

| Katman/Bileşen | Bağımlılık | Açıklama |
|----------------|-----------|----------|
| K8 | User Service | Kullanıcı CRUD |
| K8 | System Service | Sistem durumu, ayarlar |
| K5 | Veri Yönetimi | Veritabanı erişimi |
| K7 | Auth Middleware | Kimlik doğrulama |
| K0 | Dosya Sistemi | Log dosyaları, yedekleme |
| K10 | Theme Engine | Admin teması |
| K10 | Notification | Sistem bildirimleri |

## Durum: Implementasyon

**Durum**: 🟡 Planlama Aşamasında
**Öncelik**: Yüksek
**Kapsam**: Dashboard, User CRUD, RBAC, Settings, Logs, Analytics
**Test Kapsamı**: Unit test, Integration test (RBAC), E2E test (User management workflow)

<?php declare(strict_types=1);

/**
 * CoreMusic — Gender-Based Social OAuth Platform Configuration
 *
 * Cinsiyete göre sosyal medya platform listesi.
 * ADR-088 compliant — Statista/DataReportal/GWI Nisan 2026 verileri.
 *
 * @see [[decisions/accepted/ADR-088-gender-based-social-oauth]]
 * @see [[decisions/accepted/ADR-044-dynamic-user-theme-engine]]
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Gender-Based Platform Mapping
    |--------------------------------------------------------------------------
    |
    | Her cinsiyet için OAuth destekli sosyal medya platformları.
    | priority: gösterim sırası (1=en yüksek)
    | female_percent / male_percent: platform cinsiyet dağılımı
    |
    */

    'female' => [
        'platforms' => [
            'pinterest' => [
                'name' => 'Pinterest',
                'icon' => 'pinterest',
                'color' => '#E60023',
                'female_percent' => 69.4,
                'oauth_version' => '2.0',
                'authorization_url' => 'https://www.pinterest.com/oauth/',
                'token_url' => 'https://api.pinterest.com/v5/oauth/token',
                'api_base' => 'https://api.pinterest.com/v5',
                'scopes' => ['boards:read', 'pins:read', 'user_accounts:read'],
                'token_lifetime' => 30 * 24 * 3600, // 30 gün
                'refresh_lifetime' => 365 * 24 * 3600, // 365 gün
                'auth_style' => 'basic', // HTTP Basic auth
                'priority' => 1,
            ],
            'instagram' => [
                'name' => 'Instagram',
                'icon' => 'instagram',
                'color' => '#E4405F',
                'female_percent' => 58.2,
                'oauth_version' => '2.0',
                'authorization_url' => 'https://www.facebook.com/v22.0/dialog/oauth',
                'token_url' => 'https://api.instagram.com/oauth/access_token',
                'exchange_url' => 'https://graph.facebook.com/v22.0/oauth/access_token',
                'api_base' => 'https://graph.facebook.com/v22.0',
                'scopes' => ['instagram_basic', 'instagram_content_publish', 'pages_show_list'],
                'requires_business' => true,
                'requires_facebook_page' => true,
                'short_lived_token' => 3600, // 1 saat
                'long_lived_token' => 60 * 24 * 3600, // 60 gün
                'priority' => 2,
            ],
            'tiktok' => [
                'name' => 'TikTok',
                'icon' => 'tiktok',
                'color' => '#000000',
                'female_percent' => 54.0,
                'oauth_version' => '2.0',
                'authorization_url' => 'https://www.tiktok.com/v2/auth/authorize/',
                'token_url' => 'https://open.tiktokapis.com/v2/oauth/token/',
                'api_base' => 'https://open.tiktokapis.com/v2',
                'scopes' => ['user.info.basic', 'video.list', 'video.publish'],
                'access_token_lifetime' => 24 * 3600, // 24 saat
                'refresh_token_lifetime' => 365 * 24 * 3600, // 365 gün
                'priority' => 3,
            ],
            'snapchat' => [
                'name' => 'Snapchat',
                'icon' => 'snapchat',
                'color' => '#FFFC00',
                'female_percent' => 52.6,
                'oauth_version' => '2.0',
                'authorization_url' => 'https://accounts.snapchat.com/login/oauth2/authorize',
                'token_url' => 'https://accounts.snapchat.com/login/oauth2/access_token',
                'api_base' => 'https://api.snapkit.com/v1',
                'scopes' => ['user.display_name', 'bitmoji.avatar'],
                'access_token_lifetime' => 30 * 24 * 3600, // 30 gün
                'refresh_token_lifetime' => 365 * 24 * 3600, // 365 gün
                'priority' => 4,
            ],
            'youtube' => [
                'name' => 'YouTube',
                'icon' => 'youtube',
                'color' => '#FF0000',
                'female_percent' => 45.2,
                'oauth_version' => '2.0',
                'authorization_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'token_url' => 'https://oauth2.googleapis.com/token',
                'api_base' => 'https://www.googleapis.com/youtube/v3',
                'scopes' => ['https://www.googleapis.com/auth/youtube.readonly'],
                'access_type' => 'offline', // refresh token için
                'priority' => 5,
            ],
        ],
    ],

    'male' => [
        'platforms' => [
            'discord' => [
                'name' => 'Discord',
                'icon' => 'discord',
                'color' => '#5865F2',
                'male_percent' => 67.4,
                'oauth_version' => '2.0',
                'authorization_url' => 'https://discord.com/api/oauth2/authorize',
                'token_url' => 'https://discord.com/api/oauth2/token',
                'api_base' => 'https://discord.com/api/v10',
                'scopes' => ['identify', 'email', 'guilds'],
                'priority' => 1,
            ],
            'reddit' => [
                'name' => 'Reddit',
                'icon' => 'reddit',
                'color' => '#FF4500',
                'male_percent' => 61.8,
                'oauth_version' => '2.0',
                'authorization_url' => 'https://www.reddit.com/api/v1/authorize',
                'token_url' => 'https://www.reddit.com/api/v1/access_token',
                'api_base' => 'https://oauth.reddit.com/api/v1',
                'scopes' => ['identity', 'read'],
                'auth_style' => 'basic', // HTTP Basic auth
                'priority' => 2,
            ],
            'x' => [
                'name' => 'X (Twitter)',
                'icon' => 'x-twitter',
                'color' => '#000000',
                'male_percent' => 61.6,
                'oauth_version' => '2.0',
                'pkce_required' => true,
                'authorization_url' => 'https://twitter.com/i/oauth2/authorize',
                'token_url' => 'https://api.twitter.com/2/oauth2/token',
                'revoke_url' => 'https://api.twitter.com/2/oauth2/revoke',
                'api_base' => 'https://api.twitter.com/2',
                'scopes' => ['tweet.read', 'users.read', 'offline.access'],
                'access_token_lifetime' => 2 * 3600, // 2 saat
                'refresh_token_lifetime' => 60 * 24 * 3600, // 60 gün
                'requires_pkce' => true,
                'token_format' => 'jwt', // JWT token
                'priority' => 3,
            ],
            'linkedin' => [
                'name' => 'LinkedIn',
                'icon' => 'linkedin',
                'color' => '#0A66C2',
                'male_percent' => 61.4,
                'oauth_version' => '2.0',
                'authorization_url' => 'https://www.linkedin.com/oauth/v2/authorization',
                'token_url' => 'https://www.linkedin.com/oauth/v2/accessToken',
                'api_base' => 'https://api.linkedin.com/v2',
                'scopes' => ['r_liteprofile', 'r_emailaddress'],
                'priority' => 4,
            ],
            'youtube' => [
                'name' => 'YouTube',
                'icon' => 'youtube',
                'color' => '#FF0000',
                'male_percent' => 54.8,
                'oauth_version' => '2.0',
                'authorization_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'token_url' => 'https://oauth2.googleapis.com/token',
                'api_base' => 'https://www.googleapis.com/youtube/v3',
                'scopes' => ['https://www.googleapis.com/auth/youtube.readonly'],
                'access_type' => 'offline',
                'priority' => 5,
            ],
        ],
    ],

    'neutral' => [
        'platforms' => [
            'youtube' => [
                'name' => 'YouTube',
                'icon' => 'youtube',
                'color' => '#FF0000',
                'oauth_version' => '2.0',
                'authorization_url' => 'https://accounts.google.com/o/oauth2/v2/auth',
                'token_url' => 'https://oauth2.googleapis.com/token',
                'api_base' => 'https://www.googleapis.com/youtube/v3',
                'scopes' => ['https://www.googleapis.com/auth/youtube.readonly'],
                'access_type' => 'offline',
            ],
            'facebook' => [
                'name' => 'Facebook',
                'icon' => 'facebook',
                'color' => '#1877F2',
                'oauth_version' => '2.0',
                'authorization_url' => 'https://www.facebook.com/v22.0/dialog/oauth',
                'token_url' => 'https://graph.facebook.com/v22.0/oauth/access_token',
                'api_base' => 'https://graph.facebook.com/v22.0',
                'scopes' => ['public_profile', 'email'],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment Variables Mapping
    |--------------------------------------------------------------------------
    |
    | Her platform için .env değişkenleri.
    | Credential'lar ASLA kodda hardcoded edilmez.
    |
    */
    'env_mapping' => [
        'pinterest' => [
            'client_id' => 'OAUTH_PINTEREST_CLIENT_ID',
            'client_secret' => 'OAUTH_PINTEREST_CLIENT_SECRET',
        ],
        'instagram' => [
            'client_id' => 'OAUTH_INSTAGRAM_CLIENT_ID',
            'client_secret' => 'OAUTH_INSTAGRAM_CLIENT_SECRET',
        ],
        'tiktok' => [
            'client_key' => 'OAUTH_TIKTOK_CLIENT_KEY',
            'client_secret' => 'OAUTH_TIKTOK_CLIENT_SECRET',
        ],
        'snapchat' => [
            'client_id' => 'OAUTH_SNAPCHAT_CLIENT_ID',
            'client_secret' => 'OAUTH_SNAPCHAT_CLIENT_SECRET',
        ],
        'youtube' => [
            'client_id' => 'OAUTH_YOUTUBE_CLIENT_ID',
            'client_secret' => 'OAUTH_YOUTUBE_CLIENT_SECRET',
        ],
        'discord' => [
            'client_id' => 'OAUTH_DISCORD_CLIENT_ID',
            'client_secret' => 'OAUTH_DISCORD_CLIENT_SECRET',
        ],
        'reddit' => [
            'client_id' => 'OAUTH_REDDIT_CLIENT_ID',
            'client_secret' => 'OAUTH_REDDIT_CLIENT_SECRET',
        ],
        'x' => [
            'client_id' => 'OAUTH_X_CLIENT_ID',
            'client_secret' => 'OAUTH_X_CLIENT_SECRET',
        ],
        'linkedin' => [
            'client_id' => 'OAUTH_LINKEDIN_CLIENT_ID',
            'client_secret' => 'OAUTH_LINKEDIN_CLIENT_SECRET',
        ],
        'facebook' => [
            'client_id' => 'OAUTH_FACEBOOK_CLIENT_ID',
            'client_secret' => 'OAUTH_FACEBOOK_CLIENT_SECRET',
        ],
    ],
];

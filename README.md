# MLM Application - Backend (Sedekah AI)

Multi-Level Marketing (MLM) application with PIN-based registration system, flexible network hierarchy, commission calculation, and wallet management built on Laravel 12.

## Tech Stack

- **Framework**: Laravel 12
- **Database**: MySQL
- **Authorization**: Spatie Laravel Permission
- **PHP Version**: ^8.2

## Features

- PIN-based registration system (purchase, transfer, redemption)
- Marketing PIN system for special registrations (no PIN deduction, no commission)
- Flexible network hierarchy with sponsor and upline placement
- 8-level commission distribution system
- Marketing member logic (stops upward commission but receives downward)
- Digital wallet management with transaction tracking
- Withdrawal request system
- Configurable commission rates and app settings

## Installation

### Prerequisites

- PHP >= 8.2
- Composer
- MySQL

### Steps

1. Clone the repository:
```bash
git clone https://github.com/wafistudent/sedekah_ai.git
cd sedekah_ai
```

2. Install dependencies:
```bash
composer install
```

3. Configure environment:
```bash
cp .env.example .env
```

4. Update `.env` with your database credentials:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

5. Generate application key:
```bash
php artisan key:generate
```

6. Run migrations:
```bash
php artisan migrate
```

7. Seed the database:
```bash
php artisan db:seed
```

## Default Credentials

**Admin Access:**
- Email: `admin@sedekah.ai`
- Password: `password`
- Username: `admin`

## Database Schema

### Users Table
- Primary Key: `id` (varchar - username)
- Stores user information, PIN points, and DANA account details
- Relationships: network, wallet, PIN transactions, withdrawal requests

### Network Table
- Primary Key: `id` (UUID)
- Tracks MLM network hierarchy
- Fields:
  - `sponsor_id`: Who registered the member
  - `upline_id`: Position in network tree (flexible placement)
  - `is_marketing`: Marketing member flag

### PIN Transactions Table
- Primary Key: `id` (UUID)
- Tracks PIN purchase, transfer, and redemption
- Types: purchase, transfer, reedem

### Wallets Table
- Primary Key: `id` (UUID)
- One wallet per user with balance tracking

### Wallet Transactions Table
- Primary Key: `id` (UUID)
- Tracks all credit/debit operations
- Reference types: commission, withdrawal, registration_fee, adjustment

### Commission Config Table
- Primary Key: `level` (integer 1-8)
- Configurable commission amounts per level

### Withdrawal Requests Table
- Primary Key: `id` (UUID)
- Tracks withdrawal requests with approval workflow

### Marketing Pins Table
- Primary Key: `id` (UUID)
- Stores marketing PIN codes for special registration
- Format: `sedXXXX` (4 random alphanumeric characters)
- Fields:
  - `admin_id`: Admin who generated the PIN
  - `designated_member_id`: Optional tracking field
  - `redeemed_by_member_id`: Member who used this PIN
  - `status`: active, used, expired
  - `expired_at`: Optional expiration date

### App Settings Table
- Primary Key: `key` (string)
- Stores application-wide settings with type casting

## Business Logic

### PIN System

**Purchase**: Admin gives PIN points to members
```php
$pinService->purchasePin($memberId, $points, $description);
```

**Transfer**: Members transfer PIN points to each other
```php
$pinService->transferPin($fromMemberId, $toMemberId, $points);
```

**Redemption**: Sponsor redeems 1 PIN to register a new member
```php
$pinService->reedemPin($sponsorId, $newMemberData, $uplineId);
```

**Marketing PIN Registration**: Admin generates special PINs for marketing member registration (no PIN deduction, no commission)
```php
// Generate marketing PIN
$marketingPinService = app(MarketingPinService::class);
$pin = $marketingPinService->generatePin($adminId);

// Register member using marketing PIN
$newMember = $pinService->reedemPin(
    sponsorId: $sponsorId,
    newMemberData: $memberData,
    uplineId: $uplineId,
    isMarketing: true,
    marketingPinCode: $pin->code
);
```

### Network Hierarchy

**Sponsor vs Upline:**
- **Sponsor**: The member who registered (referred) the new member
- **Upline**: The member's position in the network tree (can be different from sponsor)
- **Flexible Placement**: Sponsor can place new member under any member in their network (max 8 levels deep)

**Validation:**
- Maximum network depth: 8 levels
- Upline placement validated before network creation

### Commission Distribution

**8-Level Commission System:**

When a new member registers, commissions are distributed up to 8 levels:

| Level | Default Amount |
|-------|----------------|
| 1     | Rp 40,000     |
| 2     | Rp 10,000     |
| 3     | Rp 2,000      |
| 4-8   | Rp 0          |

**Commission Flow:**
1. New member registers (via PIN redemption)
2. System gets upline chain (max 8 levels)
3. For each level, check commission_config
4. Credit commission to eligible upline's wallet
5. Skip marketing members (see below)

### Marketing Member Behavior

**Critical Logic:**

Members with `is_marketing = true` have special behavior:

- **STOP upward commission**: When a marketing member registers, NO commission is distributed to their uplines
- **RECEIVE downward commission**: Marketing members STILL RECEIVE commission from their downlines

**Example:**
```
A (normal) → B (marketing) → C (normal)

When C registers:
- B receives commission (from downline C)
- A receives NO commission (B is marketing, stops the chain)

When B registers:
- A receives NO commission (B is marketing)
```

### Registration Fee Flow

When a new member registers:
1. Sponsor redeems 1 PIN
2. Registration fee (default: Rp 20,000) is automatically credited to admin wallet
3. Commission is calculated and distributed to eligible uplines

### Wallet Operations

**Credit (Add Balance):**
```php
$walletService->credit(
    userId: $userId,
    amount: $amount,
    referenceType: 'commission',
    referenceId: $referenceId,
    fromMemberId: $fromMemberId,
    level: $level,
    description: $description
);
```

**Debit (Deduct Balance):**
```php
$walletService->debit(
    userId: $userId,
    amount: $amount,
    referenceType: 'withdrawal',
    referenceId: $referenceId,
    description: $description
);
```

**Check Withdrawal Eligibility:**
```php
$canWithdraw = $walletService->canWithdraw($userId, $amount);
// Checks: balance >= amount AND amount >= min_withdrawal
```

## Service Classes

### NetworkService
- `getUplineDepth(string $memberId): int`
- `validateUplinePlacement(string $uplineId): bool`
- `getUplineChain(string $memberId, int $maxLevel = 8): array`
- `createNetwork(...): Network`

### PinService
- `purchasePin(string $memberId, int $points, ?string $description): PinTransaction`
- `transferPin(string $fromMemberId, string $toMemberId, int $points): PinTransaction`
- `reedemPin(string $sponsorId, array $newMemberData, string $uplineId): User`

### CommissionService
- `calculateCommission(string $newMemberId): void`

### WalletService
- `createWallet(string $userId): Wallet`
- `credit(...): WalletTransaction`
- `debit(...): WalletTransaction`
- `getBalance(string $userId): float`
- `canWithdraw(string $userId, float $amount): bool`

### MarketingPinService
- `generatePin(string $adminId, ?string $designatedMemberId, ?string $expiredAt): MarketingPin`
- `generateBulkPins(string $adminId, int $quantity, ?string $designatedMemberId, ?string $expiredAt): array`
- `validatePin(string $code): array`
- `usePin(string $code, string $newMemberId): bool`

## Usage Examples

### Register a New Member

```php
use App\Services\PinService;

$pinService = app(PinService::class);

$newMember = $pinService->reedemPin(
    sponsorId: 'sponsor_username',
    newMemberData: [
        'id' => 'newuser123',
        'email' => 'newuser@example.com',
        'password' => 'password',
        'name' => 'New User',
        'phone' => '081234567890',
        'dana_name' => 'New User',
        'dana_number' => '081234567890',
        'is_marketing' => false,
    ],
    uplineId: 'upline_username'
);
```

### Get Network Upline Chain

```php
use App\Services\NetworkService;

$networkService = app(NetworkService::class);

$uplineChain = $networkService->getUplineChain('member_username', 8);
// Returns: ['level1_id', 'level2_id', 'level3_id', ...]
```

### Check Wallet Balance

```php
use App\Services\WalletService;

$walletService = app(WalletService::class);

$balance = $walletService->getBalance('member_username');
```

### Generate and Use Marketing PINs

```php
use App\Services\MarketingPinService;
use App\Services\PinService;

$marketingPinService = app(MarketingPinService::class);
$pinService = app(PinService::class);

// Generate a single marketing PIN
$pin = $marketingPinService->generatePin(
    adminId: 'admin',
    designatedMemberId: null, // optional tracking
    expiredAt: null // optional expiration date
);
echo "Generated PIN: {$pin->code}";

// Generate bulk marketing PINs
$pins = $marketingPinService->generateBulkPins(
    adminId: 'admin',
    quantity: 10
);

// Validate a marketing PIN
$validation = $marketingPinService->validatePin('sedABCD');
if ($validation['valid']) {
    echo "PIN is valid!";
}

// Register a member using marketing PIN
$newMember = $pinService->reedemPin(
    sponsorId: 'sponsor_username',
    newMemberData: [
        'id' => 'newmarketing123',
        'email' => 'marketing@example.com',
        'password' => 'password',
        'name' => 'Marketing Member',
        'phone' => '081234567890',
        'dana_name' => 'Marketing Member',
        'dana_number' => '081234567890',
        'is_marketing' => true,
    ],
    uplineId: 'upline_username',
    isMarketing: true,
    marketingPinCode: $pin->code
);
// Note: This will NOT deduct PIN from sponsor
// Note: This will NOT distribute commission to uplines
```

## Configuration

### Adjusting Commission Rates

Update the `commission_config` table:

```php
use App\Models\CommissionConfig;

CommissionConfig::updateOrCreate(
    ['level' => 1],
    ['amount' => 50000, 'is_active' => true]
);
```

### Adjusting App Settings

```php
use App\Models\AppSetting;

// Update registration fee
AppSetting::set('registration_fee', 25000, 'decimal');

// Update minimum withdrawal
AppSetting::set('min_withdrawal', 100000, 'decimal');
```

## Development

### Running Tests

```bash
php artisan test
```

### Code Style

This project follows PSR-12 coding standards. Use Laravel Pint for formatting:

```bash
./vendor/bin/pint
```

## License

MIT License

## Support

For issues and questions, please open an issue on GitHub.


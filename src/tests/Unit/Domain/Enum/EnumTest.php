<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Enum;

use App\Domain\Enum\PhotoCategory;
use App\Domain\Enum\Prefecture;
use App\Domain\Enum\ProposalStatus;
use App\Domain\Enum\SentoStatus;
use App\Domain\Enum\UserRole;
use PHPUnit\Framework\TestCase;

class EnumTest extends TestCase
{
    public function test_user_role_is_admin(): void
    {
        $this->assertTrue(UserRole::Admin->isAdmin());
        $this->assertFalse(UserRole::Member->isAdmin());
    }

    public function test_sento_status_label(): void
    {
        $this->assertSame('営業中', SentoStatus::Open->label());
        $this->assertSame('休業中', SentoStatus::ClosedTemp->label());
        $this->assertSame('廃業', SentoStatus::ClosedPerm->label());
    }

    public function test_photo_category_label(): void
    {
        $this->assertSame('外観', PhotoCategory::Exterior->label());
        $this->assertSame('内部', PhotoCategory::Interior->label());
        $this->assertSame('ロッカー', PhotoCategory::Locker->label());
        $this->assertSame('その他', PhotoCategory::Other->label());
    }

    public function test_prefecture_japanese_name(): void
    {
        $this->assertSame('東京都', Prefecture::Tokyo->jaName());
        $this->assertSame('神奈川県', Prefecture::Kanagawa->jaName());
        $this->assertSame('埼玉県', Prefecture::Saitama->jaName());
        $this->assertSame('千葉県', Prefecture::Chiba->jaName());
    }

    public function test_proposal_status_values(): void
    {
        $this->assertSame('pending', ProposalStatus::Pending->value);
        $this->assertSame('approved', ProposalStatus::Approved->value);
        $this->assertSame('rejected', ProposalStatus::Rejected->value);
    }

    public function test_prefecture_try_from_unknown_returns_null(): void
    {
        $this->assertNull(Prefecture::tryFrom('osaka'));
        $this->assertSame(Prefecture::Tokyo, Prefecture::tryFrom('tokyo'));
    }
}

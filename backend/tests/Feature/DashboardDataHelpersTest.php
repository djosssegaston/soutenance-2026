<?php

namespace Tests\Feature;

use App\Support\DashboardDataHelpers;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardDataHelpersTest extends TestCase
{
    use WithFaker;

    private $helper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->helper = new class
        {
            use DashboardDataHelpers;

            public function publicBuildTrend(float $current, float $previous, string $suffix, bool $invert = false): array
            {
                return $this->buildTrend($current, $previous, $suffix, $invert);
            }

            public function publicMonthShort(Carbon $date): string
            {
                return $this->monthShort($date);
            }

            public function publicFormatMoney(int|float|string $amount): string
            {
                return $this->formatMoney($amount);
            }

            public function publicFormatMoneyCompact(int|float|string $amount): string
            {
                return $this->formatMoneyCompact($amount);
            }

            public function publicProjectCode(int $id): string
            {
                return $this->projectCode($id);
            }

            public function publicInititials(string $name): string
            {
                return $this->initials($name);
            }

            public function publicPercent(int $value, int $total): int
            {
                return $this->percent($value, $total);
            }
        };
    }

    public function test_build_trend_positive_metric(): void
    {
        $trend = $this->helper->publicBuildTrend(100, 50, '%');
        $this->assertEquals('is-up', $trend['class']);
        $this->assertEquals('bi-arrow-up-right', $trend['icon']);
        $this->assertStringContainsString('+', $trend['label']);
    }

    public function test_build_trend_negative_metric(): void
    {
        $trend = $this->helper->publicBuildTrend(30, 100, '%');
        $this->assertEquals('is-down', $trend['class']);
        $this->assertEquals('bi-arrow-down-right', $trend['icon']);
    }

    public function test_build_trend_inverted_for_negative_metrics(): void
    {
        // For metrics like "default rate" or "late payments", an increase is bad
        $trend = $this->helper->publicBuildTrend(80, 50, '%', invert: true);
        $this->assertEquals('is-down', $trend['class'], 'Default rate increase should show as down');
        $this->assertEquals('bi-arrow-down-right', $trend['icon']);

        $trend = $this->helper->publicBuildTrend(30, 50, '%', invert: true);
        $this->assertEquals('is-up', $trend['class'], 'Default rate decrease should show as up');
        $this->assertEquals('bi-arrow-up-right', $trend['icon']);
    }

    public function test_build_trend_without_suffix(): void
    {
        $trend = $this->helper->publicBuildTrend(15, 10, '');
        $this->assertEquals('+5', $trend['label']);
        $this->assertEquals('is-up', $trend['class']);
    }

    public function test_month_short_french(): void
    {
        $this->assertEquals('Jan', $this->helper->publicMonthShort(Carbon::createFromDate(2026, 1, 1)));
        $this->assertEquals('Fev', $this->helper->publicMonthShort(Carbon::createFromDate(2026, 2, 1)));
        $this->assertEquals('Mar', $this->helper->publicMonthShort(Carbon::createFromDate(2026, 3, 1)));
        $this->assertEquals('Avr', $this->helper->publicMonthShort(Carbon::createFromDate(2026, 4, 1)));
        $this->assertEquals('Mai', $this->helper->publicMonthShort(Carbon::createFromDate(2026, 5, 1)));
        $this->assertEquals('Jun', $this->helper->publicMonthShort(Carbon::createFromDate(2026, 6, 1)));
        $this->assertEquals('Juil', $this->helper->publicMonthShort(Carbon::createFromDate(2026, 7, 1)));
        $this->assertEquals('Aou', $this->helper->publicMonthShort(Carbon::createFromDate(2026, 8, 1)));
        $this->assertEquals('Sep', $this->helper->publicMonthShort(Carbon::createFromDate(2026, 9, 1)));
        $this->assertEquals('Oct', $this->helper->publicMonthShort(Carbon::createFromDate(2026, 10, 1)));
        $this->assertEquals('Nov', $this->helper->publicMonthShort(Carbon::createFromDate(2026, 11, 1)));
        $this->assertEquals('Dec', $this->helper->publicMonthShort(Carbon::createFromDate(2026, 12, 1)));
    }

    public function test_format_money(): void
    {
        $this->assertStringContainsString('FCFA', $this->helper->publicFormatMoney(1500000));
        $this->assertStringContainsString('1 500 000', $this->helper->publicFormatMoney(1500000));
        $this->assertStringContainsString('0 FCFA', $this->helper->publicFormatMoney(0));
    }

    public function test_format_money_compact(): void
    {
        $this->assertStringContainsString('Mrd', $this->helper->publicFormatMoneyCompact(2000000000));
        $this->assertStringContainsString('M', $this->helper->publicFormatMoneyCompact(5000000));
        $this->assertStringContainsString('K', $this->helper->publicFormatMoneyCompact(50000));
    }

    public function test_project_code(): void
    {
        $this->assertEquals('PRJ-001', $this->helper->publicProjectCode(1));
        $this->assertEquals('PRJ-042', $this->helper->publicProjectCode(42));
        $this->assertEquals('PRJ-999', $this->helper->publicProjectCode(999));
    }

    public function test_initials(): void
    {
        $this->assertEquals('JD', $this->helper->publicInititials('John Doe'));
        $this->assertEquals('A', $this->helper->publicInititials('Alice'));
        $this->assertEquals('--', $this->helper->publicInititials(''));
    }

    public function test_percent(): void
    {
        $this->assertEquals(50, $this->helper->publicPercent(5, 10));
        $this->assertEquals(0, $this->helper->publicPercent(0, 10));
        $this->assertEquals(0, $this->helper->publicPercent(5, 0));
        $this->assertEquals(100, $this->helper->publicPercent(10, 10));
    }

    public function test_build_trend_edge_cases(): void
    {
        $trend = $this->helper->publicBuildTrend(0, 0, '%');
        $this->assertEquals('+0.0%', $trend['label']);

        $trend = $this->helper->publicBuildTrend(100, 0, '%');
        $this->assertStringContainsString('+', $trend['label']);

        $trend = $this->helper->publicBuildTrend(0, 100, '%');
        $this->assertStringContainsString('-', $trend['label']);
    }
}

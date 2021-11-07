<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeyStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key_statistics', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable;
            $table->text('description')->nullable();
            $table->string('country')->nullable();
            $table->string('symbol')->nullable();
            $table->string('exchange')->nullable();
            $table->string('industry')->nullable();
            $table->string('sector')->nullable();
            $table->string('qfs_symbol')->unique();
            $table->string('price')->nullable();
            $table->string('market_cap')->nullable();
            $table->json('enterprise_value')->nullable();
            $table->string('volume')->nullable();
            $table->string('average_daily_volume')->nullable();
            $table->string('volume_inc_dec')->nullable();
            $table->string('shares_basic')->nullable();
            $table->string('shares_diluted')->nullable();
            $table->string('pe_ratio')->nullable();
            $table->string('ps_ratio')->nullable();
            $table->string('pb_ratio')->nullable();
            $table->string('cogs')->nullable();
            $table->json('gross_profit')->nullable();
            $table->json('total_opex')->nullable();
            $table->json('operating_income')->nullable();
            $table->json('operating_margin')->nullable();
            $table->json('pretax_income')->nullable();
            $table->json('net_income')->nullable();
            $table->json('net_income_margin')->nullable();
            $table->json('total_current_assets')->nullable();
            $table->json('total_current_liabilities')->nullable();
            $table->json('total_assets')->nullable();
            $table->json('total_liabilities')->nullable();
            $table->json('revenue_growth')->nullable();
            $table->json('fcf_margin')->nullable();
            $table->json('roe')->nullable();
            $table->json('roa')->nullable();
            $table->json('roic')->nullable();
            $table->json('roce')->nullable();
            $table->json('rotce')->nullable();
            $table->json('dividends_per_share_cagr_10')->nullable();
            $table->json('payout_ratio')->nullable();
            $table->json('debt_to_equity')->nullable();
            $table->json('debt_to_assets')->nullable();
            $table->json('equity_to_assets')->nullable();
            $table->json('assets_to_equity')->nullable();
            $table->json('revenue_per_share')->nullable();
            $table->json('ebitda_per_share')->nullable();
            $table->json('operating_income_per_share')->nullable();
            $table->json('pretax_income_per_share')->nullable();
            $table->json('fcf_per_share')->nullable();
            $table->json('book_value_per_share')->nullable();
            $table->json('shares_eop_growth')->nullable();
            $table->json('net_income_growth')->nullable();
            $table->json('gross_profit_growth')->nullable();
            $table->json('fcf_growth')->nullable();
            $table->json('ebitda_growth')->nullable();
            $table->json('operating_income_growth')->nullable();
            $table->json('total_assets_growth')->nullable();
            $table->json('total_equity_growth')->nullable();
            $table->json('cfo_growth')->nullable();
            $table->json('revenue_cagr_10')->nullable();
            $table->json('eps_diluted_cagr_10')->nullable();
            $table->json('total_assets_cagr_10')->nullable();
            $table->json('total_equity_cagr_10')->nullable();
            $table->json('fcf_cagr_10')->nullable();
            $table->json('price_to_earnings')->nullable();
            $table->json('price_to_sales')->nullable();
            $table->json('dividends')->nullable();
            $table->json('roe_median')->nullable();
            $table->json('price_to_book')->nullable();
            $table->json('enterprise_value_to_earnings')->nullable();
            $table->json('enterprise_value_to_sales')->nullable();
            $table->json('enterprise_value_to_pretax_income')->nullable();
            $table->json('enterprise_value_to_fcf')->nullable();
            $table->json('roa_median')->nullable();
            $table->json('roic_median')->nullable();
            $table->json('gross_margin_median')->nullable();
            $table->json('pretax_margin_median')->nullable();
            $table->json('fcf_margin_median')->nullable();
            $table->json('assets_to_equity_median')->nullable();
            $table->json('debt_to_equity_median')->nullable();
            $table->json('debt_to_assets_median')->nullable();
            $table->json('revenue')->nullable();
            $table->json('gross_margin')->nullable();
            $table->json('eps_diluted')->nullable();
            $table->json('eps_diluted_growth')->nullable();
            $table->json('dividends_per_share_growth')->nullable();
            $table->json('dividends_quarterly')->nullable();
            $table->json('dividends_annual')->nullable();
            $table->string('beta')->nullable();





            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('key_statistics');
    }
}

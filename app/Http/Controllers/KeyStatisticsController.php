<?php

namespace App\Http\Controllers;

use App\Models\KeyStatistics;
use Illuminate\Http\Request;
use App\Models\Requests;
use App\Models\Tier;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class KeyStatisticsController extends Controller
{
    public function create(Request $request)
    {


        $user = Auth::user();
        if (!$user) {
            return $this->NotFoundError();
        };

        // Get current tier to get limit of requests
        $tier = Tier::find($user->tier_id);

        // Updates In User Table
        $user->monthly_number_of_requests = $user->monthly_number_of_requests + 1;
        $user->daily_number_of_requests = $user->daily_number_of_requests + 1;
        $avg_requests = ($user->monthly_number_of_requests / 30);

        // dd($user->monthly_number_of_requests / 30);

        $msg = "Sorry you can't send another request today";
        if ($tier->request_limit < $user->monthly_number_of_requests || $tier->daily_requests_limit < $user->daily_number_of_requests) {
            return $msg;
        }

        DB::table('users')->where('id', $user->id)->update([
            'monthly_number_of_requests' => $user->monthly_number_of_requests,
            'daily_number_of_requests' => $user->daily_number_of_requests,
            'avg_monthly_number_of_requests' => $avg_requests
        ]);

        // Update In Requests Table
        $currentRequest = Requests::where('id', 1)->get();
        if (!$currentRequest) {
            return $this->NotFoundError();
        };

        // Get remaining requests from quick fs
        $response = Http::get('http://public-api.quickfs.net/v1/usage?api_key=8efcfb97fcb6b39a887149f058b54f1940ccdbb8');
        $response->json();
        $responseData = $response->json();
        // dd($responseData);

        // $currentRequest[0]->monthly_number_of_requests = $currentRequest[0]->monthly_number_of_requests + 1;
        $currentRequest[0]->monthly_number_of_requests = $responseData['usage']['quota']['used'];
        // $currentRequest[0]->remaining_of_requests = $currentRequest[0]->avaliable_requests - $currentRequest[0]->monthly_number_of_requests;
        $currentRequest[0]->remaining_of_requests = $responseData['usage']['quota']['remaining'];
        $currentRequest[0]->total_daily_requests = $currentRequest[0]->total_daily_requests + 1;
        $users = User::get()->count();
        // dd($users);
        $currentRequest[0]->avg_daily_requests = ($currentRequest[0]->total_daily_requests + 1) / $users;

        $currentRequest[0]->save();



        // Angular
        $data = KeyStatistics::create([
            'name' => $request->name,
            'description' => $request->description,
            'country' => $request->country,
            'symbol' => $request->symbol,
            'exchange' => $request->exchange,
            'industry' => $request->industry,
            'sector' => $request->sector,
            'qfs_symbol' => $request->qfs_symbol,
            'price' => $request->price,
            'volume' => $request->volume,
            'market_cap' => $request->market_cap,
            'shares_basic' => $request->shares_basic,
            'shares_diluted' => $request->shares_diluted,
            'pe_ratio' => $request->pe_ratio,
            'ps_ratio' => $request->ps_ratio,
            'pb_ratio' => $request->pb_ratio,
            'enterprise_value' => $request->enterprise_value,
            'cogs' => $request->cogs,
            'gross_profit' => $request->gross_profit,
            'total_opex' => $request->total_opex,
            'operating_margin' => $request->operating_margin,
            'operating_income' => $request->operating_income,
            'pretax_income' => $request->pretax_income,
            'net_income' => $request->net_income,
            'net_income_margin' => $request->net_income_margin,
            'total_current_assets' => $request->total_current_assets,
            'total_current_liabilities' => $request->total_current_liabilities,
            'total_assets' => $request->total_assets,
            'total_liabilities' => $request->total_liabilities,
            'revenue_growth' => $request->revenue_growth,
            'fcf_margin' => $request->fcf_margin,
            'roe' => $request->roe,
            'roa' => $request->roa,
            'roic' => $request->roic,
            'roce' => $request->roce,
            'rotce' => $request->rotce,
            'dividends_per_share_cagr_10' => $request->dividends_per_share_cagr_10,
            'payout_ratio' => $request->payout_ratio,
            'debt_to_equity' => $request->debt_to_equity,
            'debt_to_assets' => $request->debt_to_assets,
            'equity_to_assets' => $request->equity_to_assets,
            'assets_to_equity' => $request->assets_to_equity,
            'revenue_per_share' => $request->revenue_per_share,
            'ebitda_per_share' => $request->ebitda_per_share,
            'operating_income_per_share' => $request->operating_income_per_share,
            'pretax_income_per_share' => $request->pretax_income_per_share,
            'fcf_per_share' => $request->fcf_per_share,
            'book_value_per_share' => $request->book_value_per_share,
            'shares_eop_growth' => $request->shares_eop_growth,
            'net_income_growth' => $request->net_income_growth,
            'gross_profit_growth' => $request->gross_profit_growth,
            'fcf_growth' => $request->fcf_growth,
            'ebitda_growth' => $request->ebitda_growth,
            'operating_income_growth' => $request->operating_income_growth,
            'total_assets_growth' => $request->total_assets_growth,
            'total_equity_growth' => $request->total_equity_growth,
            'cfo_growth' => $request->cfo_growth,
            'revenue_cagr_10' => $request->revenue_cagr_10,
            'eps_diluted_cagr_10' => $request->eps_diluted_cagr_10,
            'total_assets_cagr_10' => $request->total_assets_cagr_10,
            'total_equity_cagr_10' => $request->total_equity_cagr_10,
            'fcf_cagr_10' => $request->fcf_cagr_10,
            'dividends' => $request->dividends,
            'roe_median' => $request->roe_median,
            'price_to_book' => $request->price_to_book,
            'enterprise_value_to_earnings' => $request->enterprise_value_to_earnings,
            'enterprise_value_to_sales' => $request->enterprise_value_to_sales,
            'enterprise_value_to_pretax_income' => $request->enterprise_value_to_pretax_income,
            'enterprise_value_to_fcf' => $request->enterprise_value_to_fcf,
            'roa_median' => $request->roa_median,
            'roic_median' => $request->roic_median,
            'gross_margin_median' => $request->gross_margin_median,
            'pretax_margin_median' => $request->pretax_margin_median,
            'fcf_margin_median' => $request->fcf_margin_median,
            'assets_to_equity_median' => $request->assets_to_equity_median,
            'debt_to_equity_median' => $request->debt_to_equity_median,
            'debt_to_assets_median' => $request->debt_to_assets_median,
            'revenue' => $request->revenue,
            'gross_margin' => $request->gross_margin,
            'eps_diluted' => $request->eps_diluted,
            'eps_diluted_growth' => $request->eps_diluted_growth,
            'dividends_per_share_growth' => $request->dividends_per_share_growth,
            'dividends_quarterly' => $request->dividends_quarterly,
            'dividends_annual' => $request->dividends_annual,
            'beta' => $request->beta,
            'ev' => $request->ev,
            'avg_vol_50d' => $request->avg_vol_50d
        ]);





        return $data;
    }


    public function update(Request $request, $id)
    {

        $user = Auth::user();
        if (!$user) {
            return $this->NotFoundError();
        };

        DB::table('users')->where('id', $user->id)->update(['number_of_requests' => $user->number_of_requests + 1]);


        $company = KeyStatistics::find($id);

        if (!$company) {
            return $this->NotFoundError();
        };



        $company->name = $request->name;
        $company->description = $request->description;
        $company->country = $request->country;
        $company->symbol = $request->symbol;
        $company->exchange = $request->exchange;
        $company->industry = $request->industry;
        $company->sector = $request->sector;
        $company->qfs_symbol = $request->qfs_symbol;
        $company->price = $request->price;
        $company->pe_ratio = $request->pe_ratio;
        $company->ps_ratio = $request->ps_ratio;
        $company->pb_ratio = $request->pb_ratio;
        $company->market_cap = $request->market_cap;
        $company->enterprise_value = $request->enterprise_value;
        $company->cogs = $request->cogs;
        $company->gross_profit = $request->gross_profit;
        $company->total_opex = $request->total_opex;
        $company->operating_margin = $request->operating_margin;
        $company->operating_income = $request->operating_income;
        $company->pretax_income = $request->pretax_income;
        $company->net_income = $request->net_income;
        $company->net_income_margin = $request->net_income_margin;
        $company->total_current_assets = $request->total_current_assets;
        $company->total_current_liabilities = $request->total_current_liabilities;
        $company->total_assets = $request->total_assets;
        $company->total_liabilities = $request->total_liabilities;
        $company->revenue_growth = $request->revenue_growth;
        $company->fcf_margin = $request->fcf_margin;
        $company->roe = $request->roe;
        $company->roa = $request->roa;
        $company->roic = $request->roic;
        $company->roce = $request->roce;
        $company->rotce = $request->rotce;
        $company->dividends_per_share_cagr_10 = $request->dividends_per_share_cagr_10;
        $company->payout_ratio = $request->payout_ratio;
        $company->debt_to_equity = $request->debt_to_equity;
        $company->debt_to_assets = $request->debt_to_assets;
        $company->equity_to_assets = $request->equity_to_assets;
        $company->assets_to_equity = $request->assets_to_equity;
        $company->revenue_per_share = $request->revenue_per_share;
        $company->ebitda_per_share = $request->ebitda_per_share;
        $company->operating_income_per_share = $request->operating_income_per_share;
        $company->pretax_income_per_share = $request->pretax_income_per_share;
        $company->fcf_per_share = $request->fcf_per_share;
        $company->book_value_per_share = $request->book_value_per_share;
        $company->shares_eop_growth = $request->shares_eop_growth;
        $company->net_income_growth = $request->net_income_growth;
        $company->gross_profit_growth = $request->gross_profit_growth;
        $company->fcf_growth = $request->fcf_growth;
        $company->ebitda_growth = $request->ebitda_growth;
        $company->operating_income_growth = $request->operating_income_growth;
        $company->total_assets_growth = $request->total_assets_growth;
        $company->total_equity_growth = $request->total_equity_growth;
        $company->cfo_growth = $request->cfo_growth;
        $company->revenue_cagr_10 = $request->revenue_cagr_10;
        $company->eps_diluted_cagr_10 = $request->eps_diluted_cagr_10;
        $company->total_assets_cagr_10 = $request->total_assets_cagr_10;
        $company->total_equity_cagr_10 = $request->total_equity_cagr_10;
        $company->fcf_cagr_10 = $request->fcf_cagr_10;
        $company->price_to_earnings = $request->price_to_earnings;
        $company->price_to_sales = $request->price_to_sales;
        $company->dividends = $request->dividends;
        $company->roe_median = $request->roe_median;
        $company->price_to_book = $request->price_to_book;
        $company->enterprise_value_to_earnings = $request->enterprise_value_to_earnings;
        $company->enterprise_value_to_sales = $request->enterprise_value_to_sales;
        $company->enterprise_value_to_pretax_income = $request->enterprise_value_to_pretax_income;
        $company->enterprise_value_to_fcf = $request->enterprise_value_to_fcf;
        $company->roa_median = $request->roa_median;
        $company->roic_median = $request->roic_median;
        $company->gross_margin_median = $request->gross_margin_median;
        $company->pretax_margin_median = $request->pretax_margin_median;
        $company->fcf_margin_median = $request->fcf_margin_median;
        $company->assets_to_equity_median = $request->assets_to_equity_median;
        $company->debt_to_equity_median = $request->debt_to_equity_median;
        $company->debt_to_assets_median = $request->debt_to_assets_median;
        $company->revenue = $request->revenue;
        $company->gross_margin = $request->gross_margin;
        $company->eps_diluted = $request->eps_diluted;
        $company->eps_diluted_growth = $request->eps_diluted_growth;
        $company->dividends_per_share_growth = $request->dividends_per_share_growt;

        $company->save();

        $currentRequest = Requests::where('id', 1)->get();
        if (!$currentRequest) {
            $currentRequest[0]->number_of_requests += 1;
        };

        $currentRequest[0]->number_of_requests = $currentRequest[0]->number_of_requests + 1;

        $currentRequest[0]->save();

        return $company;
    }


    public function index()
    {
        $data = KeyStatistics::get();

        return $data;
    }

    public function show($symbol)
    {

        $data = '';

        $company = KeyStatistics::where('qfs_symbol', $symbol)->get();

        // dd(count($company));
        if (count($company) == 0) {
            return $data = 'null';
        };

        $data = $company[0];
        return $data;
    }

    public function delete($symbol)
    {
        $company = KeyStatistics::where('symbol', $symbol)->delete();


        return $company;
    }

    public function getAllNames()
    {
        $companies = KeyStatistics::get();
        $names = [];
        $symbols = [];
        foreach ($companies as $company) {
            array_push($names, $company->name);
        };

        foreach ($companies as $company) {
            array_push($symbols, $company->symbol);
        };

        $data = [
            'names' => $names,
            'symbols' => $symbols,
        ];
        return $data;
    }
}

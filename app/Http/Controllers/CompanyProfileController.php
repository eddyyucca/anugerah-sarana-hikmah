<?php

namespace App\Http\Controllers;

class CompanyProfileController extends Controller
{
    public function index()
    {
        $stats = [
            'units'    => 85,
            'years'    => 12,
            'tonnage'  => '15M+',
            'clients'  => 20,
        ];

        $chartMonths = __('company.chart_months');

        $chartTonnage = [1250000, 1380000, 1420000, 1510000, 1490000, 1620000, 1700000, 1580000, 1750000, 1820000, 1910000, 2050000];

        $chartAvailability = [91, 88, 93, 90, 94, 92, 95, 91, 96, 93, 97, 95];

        $chartDistance = [420000, 450000, 470000, 490000, 480000, 510000, 530000, 500000, 540000, 560000, 580000, 620000];

        return view('company-profile.index', compact('stats', 'chartMonths', 'chartTonnage', 'chartAvailability', 'chartDistance'));
    }
}

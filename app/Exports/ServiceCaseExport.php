<?php

namespace App\Exports;

use App\Models\ServiceCase;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ServiceCaseExport implements FromView
{
    protected $serviceCases;

    public function __construct($serviceCases)
    {
        $this->serviceCases = $serviceCases;
    }

    public function view(): View
    {
        return view('exports.service-cases', [
            'serviceCases' => $this->serviceCases
        ]);
    }
}
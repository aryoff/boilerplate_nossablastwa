<?php

namespace Modules\NossaBlastWA\Http\Controllers;

use DateTime;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Controllers\DictionaryController;
use Illuminate\Support\Facades\Log;
use Modules\IntegratedAPI\Http\Controllers\IntegratedAPIController;

class NossaBlastWAController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('nossablastwa::index');
    }

    public function APINossaTriggered(object $payload)
    {
        $searchParams = new \stdClass;
        switch ((string) $payload->level) {
            case '2':
                $searchParams->tk_subregion = $payload->tk_subregion;
                break;
            case '3':
                $searchParams->tk_region = $payload->tk_region;
                break;
            default:
                # code...
                break;
        }
        $searchParams->level = $payload->level;
        $searchParams->campaign = $payload->campaign;
        $Dictionary = new DictionaryController;
        $sendTarget = $Dictionary->retrieveValue('NossaBlastWA', 'Phone Number', $searchParams);
        $campaignBlast = $Dictionary->retrieveExtra('NossaBlastWA', 'Campaign Blast', $payload->campaign);
        if (is_null($campaignBlast) && !property_exists($campaignBlast, 'send_api_id')) {
            Log::error('No Send API ID Found for campaign ' . $payload->campaign);
            return false;
        }
        $dataTemplate =  new \stdClass;
        $dataTemplate->TICKET_ID = $payload->incident ?? '';
        $dataTemplate->TEMPLATE_DATA = array();
        $dataTemplate->TEMPLATE_DATA[] = $this->buildObject('1', 'L' . $payload->level ?? '');
        $dataTemplate->TEMPLATE_DATA[] = $this->buildObject('4', $payload->customer_type ?? '');
        $dataTemplate->TEMPLATE_DATA[] = $this->buildObject('5', $payload->incident ?? '');
        $dataTemplate->TEMPLATE_DATA[] = $this->buildObject('6', $payload->tk_subregion ?? '');
        $dataTemplate->TEMPLATE_DATA[] = $this->buildObject('7', $payload->incident_age ?? '');
        $dataTemplate->TEMPLATE_DATA[] = $this->buildObject('8', $payload->lapul ?? '');
        $dataTemplate->TEMPLATE_DATA[] = $this->buildObject('9', $payload->tk_urgensi ?? '');
        $dataTemplate->TEMPLATE_DATA[] = $this->buildObject('10', $payload->reportdate ?? '');
        $dataTemplate->TEMPLATE_DATA[] = $this->buildObject('11', $payload->serviceno ?? '');
        $dataTemplate->TEMPLATE_DATA[] = $this->buildObject('12', $payload->keluhan ?? '');
        $dataTemplate->TEMPLATE_DATA[] = $this->buildObject('13', $payload->update ?? '');
        $IntegratedAPI = new IntegratedAPIController;
        foreach ($sendTarget as $value) {
            $extraSendTarget = json_decode($value->extra);
            $data = $dataTemplate;
            $data->TEMPLATE_DATA[] = $this->buildObject('2', $extraSendTarget->jabatan ?? '');
            $date = new DateTime('now');
            $dataTemplate->TEMPLATE_DATA[] = $this->buildObject('3', $date->format('Y-m-d H:i:s'));
            $data->PHONE = $value->value;
            Log::info(json_encode($data));
            $result = $IntegratedAPI->send($campaignBlast->send_api_id, $data);
            Log::info(json_encode($result));
            //TODO result send nya simpan di database
            //TODO result send nya pakai callback ? mekanisme ???
        }
    }
    private function buildObject(string $key, string $value): object
    {
        $response = new \stdClass;
        $response->{$key} = $value;
        return $response;
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('nossablastwa::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('nossablastwa::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('nossablastwa::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
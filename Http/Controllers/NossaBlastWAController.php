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

    public function AdminContact()
    {
        return view('nossablastwa::AdminContact');
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
        $IntegratedAPI = new IntegratedAPIController;
        foreach ($sendTarget as $value) {
            $extraSendTarget = json_decode($value->extra);
            $date = new DateTime('now');
            $data =  new \stdClass;
            $data->TICKET_ID = $payload->incident ?? '';
            $data->TEMPLATE_DATA = array();
            $data->TEMPLATE_DATA[] = $this->buildObject('1', 'L' . $payload->level ?? '');
            $data->TEMPLATE_DATA[] = $this->buildObject('2', $extraSendTarget->jabatan ?? '');
            $data->TEMPLATE_DATA[] = $this->buildObject('3', $date->format('Y-m-d H:i:s'));
            $data->TEMPLATE_DATA[] = $this->buildObject('4', $payload->customer_type ?? 'null');
            $data->TEMPLATE_DATA[] = $this->buildObject('5', $payload->incident ?? 'null');
            $data->TEMPLATE_DATA[] = $this->buildObject('6', $payload->tk_subregion);
            $data->TEMPLATE_DATA[] = $this->buildObject('7', $payload->incident_age ?? 'null');
            $data->TEMPLATE_DATA[] = $this->buildObject('8', $payload->lapul ?? '0');
            $data->TEMPLATE_DATA[] = $this->buildObject('9', $payload->tk_urgensi ?? 'null');
            $data->TEMPLATE_DATA[] = $this->buildObject('10', $payload->reportdate ?? 'null');
            $data->TEMPLATE_DATA[] = $this->buildObject('11', $payload->serviceno ?? 'null');
            $data->TEMPLATE_DATA[] = $this->buildObject('12', $payload->keluhan ?? 'null');
            $data->TEMPLATE_DATA[] = $this->buildObject('13', $payload->update ?? 'null');
            $data->PHONE = $value->value;
            $result = $IntegratedAPI->send($campaignBlast->send_api_id, $data);
            Log::info(json_encode($result)); //HACK
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
    public function listDataContact(Request $request)
    {
        $data = $request->validate([
            'campaign' => 'nullable',
        ]);
        $response = new \stdClass;
        $searchExtra = new \stdClass;
        foreach ($data as $keyData => $valueData) {
            $searchExtra->{$keyData} = $valueData;
        }
        $Dictionary = new DictionaryController;
        $container = $Dictionary->retrieveValue('NossaBlastWA', 'Phone Number', $searchExtra);
        $response->data = array();
        foreach ($container as $element) {
            $tempValue = json_decode($element->extra);
            if (!property_exists($tempValue, 'tk_region')) {
                $tempValue->tk_region = null;
            }
            if (!property_exists($tempValue, 'tk_subregion')) {
                $tempValue->tk_subregion = null;
            }
            $tempValue->phone_number = $element->value;
            $response->data[] = $tempValue;
        }
        return response()->json($response, 200);
    }
    public function listDataCampaign()
    {
        $Dictionary = new DictionaryController;
        $container = $Dictionary->retrieveValue('NossaBlastWA', 'Campaign Blast');
        $response = array();
        foreach ($container as $element) {
            $response[] = $element->value;
        }
        return response()->json($response, 200);
    }
    public function addContact(Request $request)
    {
        $data = $request->validate([
            'nama' => 'required',
            'jabatan' => 'required',
            'contact_number' => 'required',
        ]);
        $Dictionary = new DictionaryController;
        $extra = new \stdClass;
        $extra->nama = $data['nama'];
        $extra->jabatan = $data['jabatan'];
        return response()->json($Dictionary->insert('NossaBlastWA', 'Phone Number', $data['contact_number'], $extra), 200);
    }
    public function updateCampaign(Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
            'nama' => 'required',
            'jabatan' => 'nullable',
            'contact_number' => 'required',
            'listWitel' => 'nullable|array',
            'listRegional' => 'nullable|array',
            'campaign' => 'nullable|array',
            'level' => 'nullable|array'
        ]);
        $Dictionary = new DictionaryController;
        $newValue = false;
        if ($data['id'] != $data['contact_number']) { //Ganti nomor
            $newValue = $Dictionary->updateValue('NossaBlastWA', 'Phone Number', $data['id'], $data['contact_number']);
        }
        $extra = new \stdClass;
        foreach ($data as $keyData => $valueData) {
            $extra->{$keyData} = $valueData;
        }
        unset($extra->id);
        unset($extra->contact_number);
        if ($newValue) {
            return response()->json($Dictionary->updateExtra('NossaBlastWA', 'Phone Number', $data['contact_number'], $extra), 200);
        } else {
            return response()->json($Dictionary->updateExtra('NossaBlastWA', 'Phone Number', $data['id'], $extra), 200);
        }
    }
}
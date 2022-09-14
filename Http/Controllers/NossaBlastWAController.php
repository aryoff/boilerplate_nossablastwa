<?php

namespace Modules\NossaBlastWA\Http\Controllers;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\DictionaryService;
use Illuminate\Support\Facades\Log;
use Modules\IntegratedAPI\Traits\IntegratedAPITrait;

define('TYPE_PHONE_NUMBER', 'Phone Number');
define('VALIDATE_NULLABLE_OR_ARRAY', 'nullable|array');
class NossaBlastWAController extends Controller
{
    use IntegratedAPITrait;

    public function AdminContact()
    {
        return view('nossablastwa::AdminContact');
    }
    public function APINossaTriggered(object $payload, DictionaryService $Dictionary = new DictionaryService)
    {
        $searchParams = ' AND ' . $this->jsonbSearchObjectConverter('level', $payload->level) . ' AND ' . $this->jsonbSearchObjectConverter('campaign', $payload->campaign) . ' AND (' . $this->jsonbSearchObjectConverter('tk_region', $payload->tk_region) . ' OR ' . $this->jsonbSearchObjectConverter('tk_subregion', $payload->tk_subregion) . ')';

        $sendTarget = $Dictionary->retrieveValue('NossaBlastWA', TYPE_PHONE_NUMBER, $searchParams);
        $campaignBlast = $Dictionary->retrieveExtra('NossaBlastWA', 'Campaign Blast', $payload->campaign);
        if (is_null($campaignBlast) || !property_exists($campaignBlast, 'send_api_id')) {
            Log::error('No Send API ID Found for campaign ' . $payload->campaign);
            return false;
        }
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
            $result = IntegratedAPITrait::send($campaignBlast->send_api_id, $data);
            Log::info(json_encode($result)); //HACK
            //TODO result send nya simpan di database
            //TODO result send nya pakai callback ? mekanisme ???
        }
        /*        if ($Dictionary->retrieveExtra('NossaBlastWA','Witel Telkom',$payload->tk_subregion)!=null) { //collect data subregion
            $Dictionary->insert('NossaBlastWA','Witel Telkom',$payload->tk_subregion);
        }*/
    }
    private function jsonbSearchObjectConverter(string $key, $value): string
    {
        if (is_numeric($value) && !is_string($value)) {
            return "(jsonb_exists(extra, '$key') AND jsonb_exists(extra->'$key', $value))";
        } else {
            return "(jsonb_exists(extra, '$key') AND jsonb_exists(extra->'$key', '$value'))";
        }
    }
    private function buildObject(string $key, string $value): object
    {
        $response = new \stdClass;
        $response->{$key} = $value;
        return $response;
    }
    public function listDataContact(DictionaryService $Dictionary)
    {
        $response = new \stdClass;
        $container = $Dictionary->retrieveValue('NossaBlastWA', TYPE_PHONE_NUMBER);
        $response->data = array();
        foreach ($container as $element) {
            $tempValue = json_decode($element->extra);
            if (!property_exists($tempValue, 'tk_region')) {
                $tempValue->tk_region = null;
            }
            if (!property_exists($tempValue, 'tk_subregion')) {
                $tempValue->tk_subregion = null;
            }
            if (!property_exists($tempValue, 'campaign')) {
                $tempValue->campaign = null;
            }
            if (!property_exists($tempValue, 'level')) {
                $tempValue->level = null;
            }
            $tempValue->phone_number = $element->value;
            $response->data[] = $tempValue;
        }
        return response()->json($response, 200);
    }
    public function listDataCampaign(DictionaryService $Dictionary)
    {
        $container = $Dictionary->retrieveValue('NossaBlastWA', 'Campaign Blast');
        $response = array();
        foreach ($container as $element) {
            $response[] = $element->value;
        }
        return response()->json($response, 200);
    }
    public function listDataWitel(DictionaryService $Dictionary)
    {
        $container = $Dictionary->retrieveValue('NossaBlastWA', 'Witel Telkom');
        $response = array();
        foreach ($container as $element) {
            $response[] = $element->value;
        }
        return response()->json($response, 200);
    }
    public function addContact(Request $request, DictionaryService $Dictionary)
    {
        $data = $request->validate([
            'nama' => 'required',
            'jabatan' => 'required',
            'contact_number' => 'required',
        ]);
        $extra = new \stdClass;
        $extra->nama = $data['nama'];
        $extra->jabatan = $data['jabatan'];
        return response()->json($Dictionary->insert('NossaBlastWA', TYPE_PHONE_NUMBER, $data['contact_number'], $extra), 200);
    }
    public function deleteContact(Request $request, DictionaryService $Dictionary)
    {
        $data = $request->validate([
            'id' => 'required',
        ]);
        return response()->json($Dictionary->deleteByValue('NossaBlastWA', TYPE_PHONE_NUMBER, $data['id']), 200);
    }
    public function updateCampaign(Request $request, DictionaryService $Dictionary)
    {
        $data = $request->validate([
            'id' => 'required',
            'nama' => 'required',
            'jabatan' => 'nullable',
            'contact_number' => 'required',
            'tk_subregion' => VALIDATE_NULLABLE_OR_ARRAY,
            'tk_region' => VALIDATE_NULLABLE_OR_ARRAY,
            'campaign' => VALIDATE_NULLABLE_OR_ARRAY,
            'level' => VALIDATE_NULLABLE_OR_ARRAY
        ]);
        $newValue = false;
        if ($data['id'] != $data['contact_number']) { //Ganti nomor
            $newValue = $Dictionary->updateValue('NossaBlastWA', TYPE_PHONE_NUMBER, $data['id'], $data['contact_number']);
        }
        $extra = new \stdClass;
        foreach ($data as $keyData => $valueData) {
            if ($keyData != 'id' && $keyData != 'contact_number' && !empty($valueData)) {
                $extra->{$keyData} = $valueData;
            }
        }
        if ($newValue) {
            return response()->json($Dictionary->updateExtra('NossaBlastWA', TYPE_PHONE_NUMBER, $data['contact_number'], $extra), 200);
        } else {
            return response()->json($Dictionary->updateExtra('NossaBlastWA', TYPE_PHONE_NUMBER, $data['id'], $extra), 200);
        }
    }
}
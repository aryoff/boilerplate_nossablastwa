<?php

namespace Modules\NossaBlastWA\Services;

use DateTime;
use App\Services\DictionaryService;
use Modules\IntegratedAPI\Services\IntegratedAPIService;
use Illuminate\Support\Facades\Log;

use function PHPUnit\Framework\isNull;

class NossaBlastWAService
{
    public function APINossaTriggered(object $payload)
    {
        $searchParams = ' AND ' . $this->jsonbSearchObjectConverter('level', $payload->level) . ' AND ' . $this->jsonbSearchObjectConverter('campaign', $payload->campaign) . ' AND (' . $this->jsonbSearchObjectConverter('tk_region', $payload->tk_region) . ' OR ' . $this->jsonbSearchObjectConverter('tk_subregion', $payload->tk_subregion) . ')';
        $Dictionary = new DictionaryService;
        $sendTarget = $Dictionary->retrieveValue('NossaBlastWA', TYPE_PHONE_NUMBER, $searchParams);
        $campaignBlast = $Dictionary->retrieveExtra('NossaBlastWA', 'Campaign Blast', $payload->campaign);
        if (is_null($campaignBlast) || !property_exists($campaignBlast, 'send_api_id')) {
            Log::error('No Send API ID Found for campaign ' . $payload->campaign);
            return false;
        }
        $result = null;
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
            $IntegratedAPI = new IntegratedAPIService;
            $result = $IntegratedAPI->send($campaignBlast->send_api_id, $data);
            // Log::info(json_encode($result)); //HACK
            //TODO result send nya simpan di database
            //TODO result send nya pakai callback ? mekanisme ???
        }
        return $result;
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
}
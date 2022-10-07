<?php

namespace Modules\NossaBlastWA\Services;

use DateTime;
use App\Services\DictionaryService;
use Modules\IntegratedAPI\Services\IntegratedAPIService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

define('DATETIME_FORMAT', 'Y-m-d H:i:s');
class NossaBlastWAService
{
    public function APINossaTriggered(object $payload)
    {
        $searchParams = ' AND ' . $this->jsonbSearchObjectConverter('level', $payload->level) . ' AND ' . $this->jsonbSearchObjectConverter('campaign', $payload->campaign) . ' AND (' . $this->jsonbSearchObjectConverter('tk_region', $payload->tk_region) . ' OR ' . $this->jsonbSearchObjectConverter('tk_subregion', $payload->tk_subregion) . ')';
        $Dictionary = new DictionaryService;
        $nossaLastHit = $Dictionary->retrieveValue('NossaBlastWA', 'NossaLastHit');
        $date = new DateTime('now');
        if (empty($nossaLastHit)) {
            $Dictionary->insert('NossaBlastWA', 'NossaLastHit', $date->format(DATETIME_FORMAT));
        } else {
            $Dictionary->updateValue('NossaBlastWA', 'NossaLastHit', $nossaLastHit[0]->value, $date->format(DATETIME_FORMAT));
        }
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
            $data = new \stdClass;
            $data->TICKET_ID = $payload->incident ?? '';
            $data->TEMPLATE_DATA = array();
            $data->TEMPLATE_DATA[] = $this->buildObject('1', 'L' . $payload->level ?? '');
            $data->TEMPLATE_DATA[] = $this->buildObject('2', $extraSendTarget->jabatan ?? '');
            $data->TEMPLATE_DATA[] = $this->buildObject('3', $date->format(DATETIME_FORMAT));
            $data->TEMPLATE_DATA[] = $this->buildObject('4', $payload->customer_type ?? 'null');
            $data->TEMPLATE_DATA[] = $this->buildObject('5', $payload->incident ?? 'null');
            $data->TEMPLATE_DATA[] = $this->buildObject('6', $payload->tk_subregion);
            $data->TEMPLATE_DATA[] = $this->buildObject('7', $payload->incident_age ?? 'null');
            $data->TEMPLATE_DATA[] = $this->buildObject('8', $payload->lapul ?? '0');
            $data->TEMPLATE_DATA[] = $this->buildObject('9', $payload->tk_urgensi ?? 'null');
            $data->TEMPLATE_DATA[] = $this->buildObject('10', $payload->reportdate ?? 'null');
            $data->TEMPLATE_DATA[] = $this->buildObject('11', $payload->serviceno ?? 'null');
            $data->TEMPLATE_DATA[] = $this->buildObject('12', $payload->keluhan ?? 'null');
            $data->TEMPLATE_DATA[] = $this->buildObject('13', $payload->update ?? '_Belum ada update tindak lanjut_');
            $data->PHONE = $value->value;
            $IntegratedAPI = new IntegratedAPIService;
            $result = $IntegratedAPI->send($campaignBlast->send_api_id, $data);
            if (is_null($result)) {
                $tempResult = new \stdClass;
                $tempResult->session_id = null;
                $tempResult->msg = 'api failed';
                $this->saveAPIResult($tempResult, $payload, $value->value, $extraSendTarget->nama);
            } else {
                //HACK
                if (!property_exists($result, 'session_id')) {
                    Log::info('[NossaBlastWA] APINossaTriggered ' . json_encode($result));
                }
                $this->saveAPIResult($result, $payload, $value->value, $extraSendTarget->nama);
            }
        }
        return $result;
    }
    private function saveAPIResult(object $result, object $payload, string $phone, string $namaPenerima): bool
    {
        $date = new DateTime('now');
        return DB::insert("INSERT INTO nossablastwa_logs (session_id,status,data) VALUES (:session_id,jsonb_build_object(:msg,:msgtime),jsonb_build_object('tk_subregion',:tk_subregion,'tk_region',:tk_region,'phone_number',:phone_number,'campaign',:campaign,'level',:lvl,'incident',:incident,'keluhan',:keluhan,'lapul',:lapul,'tk_urgensi',:tk_urgensi,'penerima',:penerima));", ['session_id' => $result->session_id, 'tk_subregion' => $payload->tk_subregion, 'tk_region' => $payload->tk_region, 'phone_number' => $phone, 'campaign' => $payload->campaign, 'lvl' => $payload->level, 'incident' => $payload->incident, 'keluhan' => $payload->keluhan, 'lapul' => $payload->lapul ?? '0', 'tk_urgensi' => $payload->tk_urgensi, 'penerima' => $namaPenerima, 'msg' => $result->msg, 'msgtime' => $date->format(DATETIME_FORMAT)]);
    }
    public function APIWACallback(object $payload)
    {
        $Dictionary = new DictionaryService;
        $apiWALastHit = $Dictionary->retrieveValue('NossaBlastWA', 'APIWACallbackLastHit');
        $date = new DateTime('now');
        if (empty($apiWALastHit)) {
            $Dictionary->insert('NossaBlastWA', 'APIWACallbackLastHit', $date->format(DATETIME_FORMAT));
        } else {
            $Dictionary->updateValue('NossaBlastWA', 'APIWACallbackLastHit', $apiWALastHit[0]->value, $date->format(DATETIME_FORMAT));
        }
        return DB::update("UPDATE nossablastwa_logs SET status=nossablastwa_logs.status||jsonb_build_object(:msg,:msgtime) WHERE session_id=:session_id", ['session_id' => $payload->session_id, 'msg' => $payload->msg, 'msgtime' => $date->format(DATETIME_FORMAT)]);
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
<?php

namespace Modules\NossaBlastWA\Traits;

use Modules\IntegratedAPI\Traits\IntegratedAPITrait;

trait NossaBlastWATrait
{
    use IntegratedAPITrait;

    public static function APINossaTriggered(object $payload)
    {
        $searchParams = ' AND ' . self::jsonbSearchObjectConverter('level', $payload->level) . ' AND ' . self::jsonbSearchObjectConverter('campaign', $payload->campaign) . ' AND (' . self::jsonbSearchObjectConverter('tk_region', $payload->tk_region) . ' OR ' . self::jsonbSearchObjectConverter('tk_subregion', $payload->tk_subregion) . ')';

        $sendTarget = DictionaryTrait::retrieveValue('NossaBlastWA', TYPE_PHONE_NUMBER, $searchParams);
        $campaignBlast = DictionaryTrait::retrieveExtra('NossaBlastWA', 'Campaign Blast', $payload->campaign);
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
            $data->TEMPLATE_DATA[] = self::buildObject('1', 'L' . $payload->level ?? '');
            $data->TEMPLATE_DATA[] = self::buildObject('2', $extraSendTarget->jabatan ?? '');
            $data->TEMPLATE_DATA[] = self::buildObject('3', $date->format('Y-m-d H:i:s'));
            $data->TEMPLATE_DATA[] = self::buildObject('4', $payload->customer_type ?? 'null');
            $data->TEMPLATE_DATA[] = self::buildObject('5', $payload->incident ?? 'null');
            $data->TEMPLATE_DATA[] = self::buildObject('6', $payload->tk_subregion);
            $data->TEMPLATE_DATA[] = self::buildObject('7', $payload->incident_age ?? 'null');
            $data->TEMPLATE_DATA[] = self::buildObject('8', $payload->lapul ?? '0');
            $data->TEMPLATE_DATA[] = self::buildObject('9', $payload->tk_urgensi ?? 'null');
            $data->TEMPLATE_DATA[] = self::buildObject('10', $payload->reportdate ?? 'null');
            $data->TEMPLATE_DATA[] = self::buildObject('11', $payload->serviceno ?? 'null');
            $data->TEMPLATE_DATA[] = self::buildObject('12', $payload->keluhan ?? 'null');
            $data->TEMPLATE_DATA[] = self::buildObject('13', $payload->update ?? 'null');
            $data->PHONE = $value->value;
            $result = IntegratedAPITrait::send($campaignBlast->send_api_id, $data);
            Log::info(json_encode($result)); //HACK
            //TODO result send nya simpan di database
            //TODO result send nya pakai callback ? mekanisme ???
        }
        /*        if (DictionaryTrait::retrieveExtra('NossaBlastWA','Witel Telkom',$payload->tk_subregion)!=null) { //collect data subregion
            DictionaryTrait::insert('NossaBlastWA','Witel Telkom',$payload->tk_subregion);
        }*/
    }
    private static function jsonbSearchObjectConverter(string $key, $value): string
    {
        if (is_numeric($value) && !is_string($value)) {
            return "(jsonb_exists(extra, '$key') AND jsonb_exists(extra->'$key', $value))";
        } else {
            return "(jsonb_exists(extra, '$key') AND jsonb_exists(extra->'$key', '$value'))";
        }
    }
    private static function buildObject(string $key, string $value): object
    {
        $response = new \stdClass;
        $response->{$key} = $value;
        return $response;
    }
}
<?php

namespace Modules\NossaBlastWA\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Services\DictionaryService;
use Modules\NossaBlastWA\Services\NossaBlastWAService;

define('VALIDATE_NULLABLE_OR_ARRAY', 'nullable|array');
class NossaBlastWAController extends Controller
{
    public function AdminContact()
    {
        return view('nossablastwa::AdminContact');
    }
    public function ViewData()
    {
        return view('nossablastwa::viewdata');
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
    public function listDataLog(Request $request, NossaBlastWAService $nossa)
    {
        $data = $request->validate([
            'draw' => 'required|integer',
            'start' => 'required|integer',
            'length' => 'required|integer',
            'search' => 'nullable|array',
            'order' => 'nullable|array',
            'columns' => 'required|array',
        ]);

        $response = $nossa->getLog($data);
        $response->draw = $data['draw'];
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
    public function findContact(Request $request, DictionaryService $Dictionary)
    {
        $data = $request->validate([
            'id' => 'required',
        ]);
        return response()->json($Dictionary->retrieveExtra('NossaBlastWA', TYPE_PHONE_NUMBER, $data['id']), 200);
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
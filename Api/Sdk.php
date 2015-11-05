<?php

namespace Kzap\Ontraport\Api;

use Exception;
use GuzzleHttp\Client;

/**
 * Application layer of Ontraport library.
 *
 * @author kzap
 */
class Sdk
{
    /* Properties
    -------------------------------*/
    private $config = null;
    public $ontraport = null;
    public $client = null;
    public $apiVersion = 1;
    public $objectTypeIds = array(
        0 => 'Contact',
        2 => 'Staff',
        3 => 'Unknown1',
        4 => 'Sent', // sent emails
        5 => 'Sequence',
        6 => 'Rules',
        7 => 'Sequence Sent', // sent emails from sequences
        8 => 'Sequence Contact Stats', // sent emails from sequences
        9 => 'Sequence Message Stats', // sent emails from sequences
        12 => 'Note',
        13 => 'Unknown2',
        14 => 'Tag',
        15 => 'Stats', // email opens and clicks i think 
        16 => 'Product',
        17 => 'Purchase',
        46 => 'Invoice',
        52 => 'Open Orders',
        63 => 'Taxes',
        64 => 'Shipping',
        70 => 'Gateway',
    );

    /* Magic Methods
    -------------------------------*/
    public function __construct($appId, $apiKey)
    {
        // define constant and get config
        $this->client = new \GuzzleHttp\Client([
            'base_url' => 'https://api.ontraport.com/'.$this->apiVersion.'/',
            'defaults' => [
                'headers' => [
                    'Api-Appid' => $appId,
                    'Api-Key' => $apiKey,
                ],
            ],

        ]);
    }

    /* Utility Methods
    -------------------------------*/

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function createObject($params = array())
    {
        // get and validate objectTypeId
        $objectTypeId = false;
        // get objectTypeId by object_type_name
        if (isset($params['object_type_name'])) {
            $objectTypeId = $this->getObjectTypeByName($params['object_type_name']);
        }
        // get objectTypeId by object_type_id
        if (isset($params['object_type_id']) && isset($this->objectTypeIds[$params['object_type_id']])) {
            $objectTypeId = $params['object_type_id'];
        }
        // get objectTypeId by objectID
        if (isset($params['objectID'])) {
            $objectTypeId = $params['objectID'];
        }
        if (is_null($objectTypeId) || $objectTypeId === false) {
            throw new Exception(__METHOD__.' needs a valid Object Type ID');
        }

        $body = array();
        $body['objectID'] = (int) $objectTypeId;
        foreach ($params as $fieldName => $fieldValue) {
            $paramsToIgnore = array(
                'objectID',
                'id',
                'object_type_name',
                'object_type_id',
            );
            if (!in_array($fieldName, $paramsToIgnore)) {
                $body[$fieldName] = $fieldValue;
            }
        }
        $request = $this->client->createRequest('POST', 'objects', ['body' => $body]);
        
        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function getObject($params = array())
    {
        // get and validate objectTypeId
        $objectTypeId = false;
        // get objectTypeId by object_type_name
        if (isset($params['object_type_name'])) {
            $objectTypeId = $this->getObjectTypeByName($params['object_type_name']);
        }
        // get objectTypeId by object_type_id
        if (isset($params['object_type_id']) && isset($this->objectTypeIds[$params['object_type_id']])) {
            $objectTypeId = $params['object_type_id'];
        }
        // get objectTypeId by objectID
        if (isset($params['objectID'])) {
            $objectTypeId = $params['objectID'];
        }
        if (is_null($objectTypeId) || $objectTypeId === false) {
            throw new Exception(__METHOD__.' needs a valid Object Type ID');
        }

        $request = $this->client->createRequest('GET', 'object');
        $query = $request->getQuery();
        $query->set('objectID', (int) $objectTypeId);
        $query->set('id', (int) $params['id']);

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function getObjects($params = array())
    {
        // get and validate objectTypeId
        $objectTypeId = false;
        // get objectTypeId by object_type_name
        if (isset($params['object_type_name'])) {
            $objectTypeId = $this->getObjectTypeByName($params['object_type_name']);
        }
        // get objectTypeId by object_type_id
        if (isset($params['object_type_id']) && isset($this->objectTypeIds[$params['object_type_id']])) {
            $objectTypeId = $params['object_type_id'];
        }
        // get objectTypeId by objectID
        if (isset($params['objectID'])) {
            $objectTypeId = $params['objectID'];
        }
        if (is_null($objectTypeId) || $objectTypeId === false) {
            throw new Exception(__METHOD__.' needs a valid Object Type ID');
        }

        $request = $this->client->createRequest('GET', 'objects');
        $query = $request->getQuery();
        $query->set('objectID', (int) $objectTypeId);
        if (isset($params['ids'])) {
            if (is_array($params['ids'])) {
                $query->set('ids', implode(',', $params['ids']));
            } else {
                $query->set('ids', $params['ids']);
            }
        }
        if (isset($params['start'])) {
            $query->set('start', $params['start']);
        }
        if (isset($params['range'])) {
            $query->set('range', $params['range']);
        }
        if (isset($params['condition'])) {
            if (is_array($params['condition'])) {
                $query->set('condition', '('.implode(') AND (', $params['condition']).')');
            } else {
                $query->set('condition', $params['condition']);
            }
        }
        // add other params
        foreach ($params as $fieldName => $fieldValue) {
            $paramsToIgnore = array(
                'objectID',
                'id',
                'ids',
                'object_type_name',
                'object_type_id',
                'start',
                'range',
                'condition',
            );
            if (!in_array($fieldName, $paramsToIgnore)) {
                $query->set($fieldName, $fieldValue);
            }
        }

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function updateObject($params = array())
    {
        // get and validate objectTypeId
        $objectTypeId = false;
        // get objectTypeId by object_type_name
        if (isset($params['object_type_name'])) {
            $objectTypeId = $this->getObjectTypeByName($params['object_type_name']);
        }
        // get objectTypeId by object_type_id
        if (isset($params['object_type_id']) && isset($this->objectTypeIds[$params['object_type_id']])) {
            $objectTypeId = $params['object_type_id'];
        }
        // get objectTypeId by objectID
        if (isset($params['objectID'])) {
            $objectTypeId = $params['objectID'];
        }
        if (is_null($objectTypeId) || $objectTypeId === false) {
            throw new Exception(__METHOD__.' needs a valid Object Type ID');
        }

        $body = array();
        $body['objectID'] = (int) $objectTypeId;
        $body['id'] = (int) $params['id'];
        foreach ($params as $fieldName => $fieldValue) {
            $paramsToIgnore = array(
                'objectID',
                'id',
                'object_type_name',
                'object_type_id',
            );
            if (!in_array($fieldName, $paramsToIgnore)) {
                $body[$fieldName] = $fieldValue;
            }
        }
        $request = $this->client->createRequest('PUT', 'objects', ['body' => $body]);

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Convenience Method
     * Insert or Update Object based on Distinct properties
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function upsertObject($params = array(), $distinctFields = array(), $idField = 'id')
    {
        // get and validate objectTypeId
        $objectTypeId = false;
        // get objectTypeId by object_type_name
        if (isset($params['object_type_name'])) {
            $objectTypeId = $this->getObjectTypeByName($params['object_type_name']);
        }
        // get objectTypeId by object_type_id
        if (isset($params['object_type_id']) && isset($this->objectTypeIds[$params['object_type_id']])) {
            $objectTypeId = $params['object_type_id'];
        }
        // get objectTypeId by objectID
        if (isset($params['objectID'])) {
            $objectTypeId = $params['objectID'];
        }
        if (is_null($objectTypeId) || $objectTypeId === false) {
            throw new Exception(__METHOD__.' needs a valid Object Type ID');
        }

        $distinctFields = (array) $distinctFields;
        if (is_null($distinctFields) || empty($distinctFields)) {
            throw new Exception(__METHOD__.' needs a $distinctFields to contain atleast 1 distinct field');
        }

        // search for matching object
        $searchParams = array(
            'objectID' => $objectTypeId,
            'condition' => array(),
        );
        foreach ($distinctFields as $distinctField) {
            if (isset($params[$distinctField])) {
                $searchParams['condition'][] = $distinctField . '="' . $params[$distinctField] . '"';
            } else {
                throw new Exception('Distinct Field value of [' . $distinctField . '] not found in $params');
            }
        }
        $searchResponse = $this->getObjects($searchParams);
        
        if (isset($searchResponse['data']) && !empty($searchResponse['data'])) {
            // if we found an object, update it
            $updateParams = $params;
            $updateParams['id'] = $searchResponse['data'][0][$idField];
            $updateResponse = $this->updateObject($updateParams);
            
            // return found data
            if (isset($updateResponse['data']['attrs']) && !empty($updateResponse['data']['attrs'])) {
                return array_merge((array) $searchResponse['data'][0], (array) $updateResponse['data']['attrs']);
            } else {
                return $searchResponse['data'][0];
            }
        } else {
            // we didnt find an object, so lets insert
            $createParams = $params;
            $createResponse = $this->createObject($createParams);
            if (isset($createResponse['data']) && !empty($createResponse['data'])) {
                // return found id
                return $createResponse['data'];
            }
        }

        return false;
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function getObjectTypes($params = array())
    {
        // get and validate objectTypeId
        $objectTypeId = false;
        // get objectTypeId by object_type_name
        if (isset($params['object_type_name'])) {
            $objectTypeId = $this->getObjectTypeByName($params['object_type_name']);
        }
        // get objectTypeId by object_type_id
        if (isset($params['object_type_id']) && isset($this->objectTypeIds[$params['object_type_id']])) {
            $objectTypeId = $params['object_type_id'];
        }
        // get objectTypeId by objectID
        if (isset($params['objectID'])) {
            $objectTypeId = $params['objectID'];
        }
        
        $request = $this->client->createRequest('GET', 'objects/meta');
        $query = $request->getQuery();
        if (isset($params['format'])) {
            $query->set('format', $params['format']);
        }
        if (!(is_null($objectTypeId) || $objectTypeId === false)) {
            $query->set('objectID', (int) $objectTypeId);
        }

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function updateObjectTags($params = array())
    {
        // get and validate objectTypeId
        $objectTypeId = false;
        // get objectTypeId by object_type_name
        if (isset($params['object_type_name'])) {
            $objectTypeId = $this->getObjectTypeByName($params['object_type_name']);
        }
        // get objectTypeId by object_type_id
        if (isset($params['object_type_id']) && isset($this->objectTypeIds[$params['object_type_id']])) {
            $objectTypeId = $params['object_type_id'];
        }
        // get objectTypeId by objectID
        if (isset($params['objectID'])) {
            $objectTypeId = $params['objectID'];
        }
        if (is_null($objectTypeId) || $objectTypeId === false) {
            throw new Exception(__METHOD__.' needs a valid Object Type ID');
        }

        $body = array();
        $body['objectID'] = (int) $objectTypeId;
        if (isset($params['ids'])) {
            if (is_array($params['ids'])) {
                $body['ids'] = implode(',', $params['ids']);
            } else {
                $body['ids'] = $params['ids'];
            }
        }
        if (isset($params['add_list'])) {
            if (is_array($params['add_list'])) {
                $body['add_list'] = implode(',', $params['add_list']);
            } else {
                $body['add_list'] = $params['add_list'];
            }
        }
        if (isset($params['start'])) {
            $body['start'] = $params['start'];
        }
        if (isset($params['range'])) {
            $body['range'] = $params['range'];
        }
        if (isset($params['condition'])) {
            if (is_array($params['condition'])) {
                $body['condition'] = '('.implode(') AND (', $params['condition']).')';
            } else {
                $body['condition'] = $params['condition'];
            }
        }
        $request = $this->client->createRequest('PUT', 'objects/tag', ['body' => $body]);

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function deleteObjectTags($params = array())
    {
        // get and validate objectTypeId
        $objectTypeId = false;
        // get objectTypeId by object_type_name
        if (isset($params['object_type_name'])) {
            $objectTypeId = $this->getObjectTypeByName($params['object_type_name']);
        }
        // get objectTypeId by object_type_id
        if (isset($params['object_type_id']) && isset($this->objectTypeIds[$params['object_type_id']])) {
            $objectTypeId = $params['object_type_id'];
        }
        // get objectTypeId by objectID
        if (isset($params['objectID'])) {
            $objectTypeId = $params['objectID'];
        }
        if (is_null($objectTypeId) || $objectTypeId === false) {
            throw new Exception(__METHOD__.' needs a valid Object Type ID');
        }

        $body = array();
        $body['objectID'] = (int) $objectTypeId;
        if (isset($params['ids'])) {
            if (is_array($params['ids'])) {
                $body['ids'] = implode(',', $params['ids']);
            } else {
                $body['ids'] = $params['ids'];
            }
        }
        if (isset($params['remove_list'])) {
            if (is_array($params['remove_list'])) {
                $body['remove_list'] = implode(',', $params['remove_list']);
            } else {
                $body['remove_list'] = $params['remove_list'];
            }
        }
        if (isset($params['start'])) {
            $body['start'] = $params['start'];
        }
        if (isset($params['range'])) {
            $body['range'] = $params['range'];
        }
        if (isset($params['condition'])) {
            if (is_array($params['condition'])) {
                $body['condition'] = '('.implode(') AND (', $params['condition']).')';
            } else {
                $body['condition'] = $params['condition'];
            }
        }
        $request = $this->client->createRequest('DELETE', 'objects/tag', ['body' => $body]);

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function getForm($params = array())
    {
        $request = $this->client->createRequest('GET', 'form');
        $query = $request->getQuery();
        $query->set('id', (int) $params['id']);

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function getMessage($params = array())
    {
        $request = $this->client->createRequest('GET', 'message');
        $query = $request->getQuery();
        $query->set('id', (int) $params['id']);

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function taskCancel($params = array())
    {
        $body = array();
        $body['objectID'] = 0;
        if (isset($params['ids'])) {
            if (is_array($params['ids'])) {
                $body['ids'] = implode(',', $params['ids']);
            } else {
                $body['ids'] = $params['ids'];
            }
        }
        if (isset($params['start'])) {
            $body['start'] = $params['start'];
        }
        if (isset($params['range'])) {
            $body['range'] = $params['range'];
        }
        if (isset($params['condition'])) {
            if (is_array($params['condition'])) {
                $body['condition'] = '('.implode(') AND (', $params['condition']).')';
            } else {
                $body['condition'] = $params['condition'];
            }
        }
        $request = $this->client->createRequest('POST', 'task/cancel', ['body' => $body]);
        
        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function taskComplete($params = array())
    {
        $body = array();
        $body['objectID'] = 0;
        if (isset($params['ids'])) {
            if (is_array($params['ids'])) {
                $body['ids'] = implode(',', $params['ids']);
            } else {
                $body['ids'] = $params['ids'];
            }
        }
        if (isset($params['start'])) {
            $body['start'] = $params['start'];
        }
        if (isset($params['range'])) {
            $body['range'] = $params['range'];
        }
        if (isset($params['condition'])) {
            if (is_array($params['condition'])) {
                $body['condition'] = '('.implode(') AND (', $params['condition']).')';
            } else {
                $body['condition'] = $params['condition'];
            }
        }
        $request = $this->client->createRequest('POST', 'task/complete', ['body' => $body]);
        
        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function createTransaction($params = array())
    {
        $body = array();
        $body['objectID'] = 0;
        foreach ($params as $fieldName => $fieldValue) {
            $paramsToIgnore = array(
                'objectID',
                'id',
                'object_type_name',
                'object_type_id',
            );
            if (!in_array($fieldName, $paramsToIgnore)) {
                $body[$fieldName] = $fieldValue;
            }
        }
        $request = $this->client->createRequest('POST', 'transaction/processManual', ['body' => $body]);

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function refundTransaction($params = array())
    {
        $body = array();
        $body['objectID'] = 0;
        if (isset($params['ids'])) {
            if (is_array($params['ids'])) {
                $body['ids'] = implode(',', $params['ids']);
            } else {
                $body['ids'] = $params['ids'];
            }
        }
        if (isset($params['start'])) {
            $body['start'] = $params['start'];
        }
        if (isset($params['range'])) {
            $body['range'] = $params['range'];
        }
        if (isset($params['condition'])) {
            if (is_array($params['condition'])) {
                $body['condition'] = '('.implode(') AND (', $params['condition']).')';
            } else {
                $body['condition'] = $params['condition'];
            }
        }
        $request = $this->client->createRequest('PUT', 'transaction/refund', ['body' => $body]);

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function declineTransaction($params = array())
    {
        $body = array();
        $body['id'] = (int) $params['id'];
        $request = $this->client->createRequest('PUT', 'transaction/convertToDecline', ['body' => $body]);
        
        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function collectTransaction($params = array())
    {
        $body = array();
        $body['id'] = (int) $params['id'];
        $request = $this->client->createRequest('PUT', 'transaction/convertToCollections', ['body' => $body]);

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function voidTransaction($params = array())
    {
        $body = array();
        $body['objectID'] = 0;
        if (isset($params['ids'])) {
            if (is_array($params['ids'])) {
                $body['ids'] = implode(',', $params['ids']);
            } else {
                $body['ids'] = $params['ids'];
            }
        }
        if (isset($params['start'])) {
            $body['start'] = $params['start'];
        }
        if (isset($params['range'])) {
            $body['range'] = $params['range'];
        }
        if (isset($params['condition'])) {
            if (is_array($params['condition'])) {
                $body['condition'] = '('.implode(') AND (', $params['condition']).')';
            } else {
                $body['condition'] = $params['condition'];
            }
        }
        $request = $this->client->createRequest('PUT', 'transaction/void', ['body' => $body]);

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function voidTransactionPurchase($params = array())
    {
        $body = array();
        $body['id'] = (int) $params['id'];
        $request = $this->client->createRequest('PUT', 'transaction/voidPurchase', ['body' => $body]);
        
        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function rerunTransactionCommission($params = array())
    {
        $body = array();
        $body['objectID'] = 0;
        if (isset($params['ids'])) {
            if (is_array($params['ids'])) {
                $body['ids'] = implode(',', $params['ids']);
            } else {
                $body['ids'] = $params['ids'];
            }
        }
        if (isset($params['start'])) {
            $body['start'] = $params['start'];
        }
        if (isset($params['range'])) {
            $body['range'] = $params['range'];
        }
        if (isset($params['condition'])) {
            if (is_array($params['condition'])) {
                $body['condition'] = '('.implode(') AND (', $params['condition']).')';
            } else {
                $body['condition'] = $params['condition'];
            }
        }
        $request = $this->client->createRequest('PUT', 'transaction/rerunCommission', ['body' => $body]);
        
        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function markTransactionPaid($params = array())
    {
        $body = array();
        $body['id'] = (int) $params['id'];
        $request = $this->client->createRequest('PUT', 'transaction/markPaid', ['body' => $body]);
        
        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function rerunTransaction($params = array())
    {
        $body = array();
        $body['objectID'] = 0;
        if (isset($params['ids'])) {
            if (is_array($params['ids'])) {
                $body['ids'] = implode(',', $params['ids']);
            } else {
                $body['ids'] = $params['ids'];
            }
        }
        if (isset($params['start'])) {
            $body['start'] = $params['start'];
        }
        if (isset($params['range'])) {
            $body['range'] = $params['range'];
        }
        if (isset($params['condition'])) {
            if (is_array($params['condition'])) {
                $body['condition'] = '('.implode(') AND (', $params['condition']).')';
            } else {
                $body['condition'] = $params['condition'];
            }
        }
        $request = $this->client->createRequest('POST', 'transaction/rerun', ['body' => $body]);

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function writeTransactionOff($params = array())
    {
        $body = array();
        $body['id'] = (int) $params['id'];
        $request = $this->client->createRequest('PUT', 'transaction/writeOff', ['body' => $body]);

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function getTransaction($params = array())
    {
        $request = $this->client->createRequest('GET', 'transaction/order');
        $query = $request->getQuery();
        $query->set('id', (int) $params['id']);

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function updateTransaction($params = array())
    {
        $body = array();
        $body['objectID'] = 0;
        foreach ($params as $fieldName => $fieldValue) {
            $paramsToIgnore = array(
                'objectID',
                'id',
                'object_type_name',
                'object_type_id',
            );
            if (!in_array($fieldName, $paramsToIgnore)) {
                $body[$fieldName] = $fieldValue;
            }
        }
        $request = $this->client->createRequest('PUT', 'transaction/order', ['body' => $body]);
        
        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    /**
     * Uses New API.
     *
     * @param array parameters
     *
     * @return scalar json response
     */
    public function resendTransactionInvoice($params = array())
    {
        $body = array();
        $body['objectID'] = 0;
        foreach ($params as $fieldName => $fieldValue) {
            $paramsToIgnore = array(
                'objectID',
                'id',
                'object_type_name',
                'object_type_id',
            );
            if (!in_array($fieldName, $paramsToIgnore)) {
                $body[$fieldName] = $fieldValue;
            }
        }
        $request = $this->client->createRequest('POST', 'transaction/resendInvoice', ['body' => $body]);

        try {
            $response = $this->client->send($request);
        } catch (RequestException $e) {
            //echo $e->getRequest() . "\n";
            if ($e->hasResponse()) {
                //echo $e->getResponse() . "\n";
            }
        }

        return $response->json();
    }

    public function getObjectTypeByName($name)
    {

        // sanitize name
        $objectTypeName = strtolower($name);

        // sanitize objectTypeIds
        $objectTypeIds = array_map('strtolower', $this->objectTypeIds);

        $objectKey = array_search($objectTypeName, $objectTypeIds);
        if ($objectKey !== false) {
            return $objectKey;
        }

        return false;
    }
}

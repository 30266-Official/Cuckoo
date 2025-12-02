<?php
require_once 'vendor/autoload.php';
use TencentCloud\Common\Credential;
use TencentCloud\Common\Profile\ClientProfile;
use TencentCloud\Common\Profile\HttpProfile;
use TencentCloud\Common\Exception\TencentCloudSDKException;
use TencentCloud\Lighthouse\V20200324\LighthouseClient;
use TencentCloud\Lighthouse\V20200324\Models\DescribeInstancesRequest;
try {
    $cred = new Credential(getenv("TENCENTCLOUD_SECRET_ID"), getenv("TENCENTCLOUD_SECRET_KEY"));
    $httpProfile = new HttpProfile();
    $httpProfile->setEndpoint("lighthouse.tencentcloudapi.com");
    $clientProfile = new ClientProfile();
    $clientProfile->setHttpProfile($httpProfile);
    $client = new LighthouseClient($cred, "", $clientProfile);
    $req = new DescribeInstancesRequest();
    $envIds = getenv('TENCENTCLOUD_INSTANCE_ID');
    $ids = array();
    if ($envIds !== false && strlen(trim($envIds)) > 0) {
        $envTrim = trim($envIds);
        if (($json = json_decode($envTrim, true)) !== null && is_array($json)) {
            $ids = $json;
        } else {
            $parts = preg_split('/[,;\s]+/', $envTrim);
            $parts = array_map('trim', $parts);
            $ids = array_values(array_filter($parts, function($v) { return $v !== ''; }));
        }
    }
    if (empty($ids)) {
        $ids = array("lhins");
    }
    $params = array(
        "InstanceIds" => $ids
    );
    $req->fromJsonString(json_encode($params));
    $resp = $client->DescribeInstances($req);
    print_r($resp->toJsonString());
}
catch(TencentCloudSDKException $e) {
    echo $e;
}
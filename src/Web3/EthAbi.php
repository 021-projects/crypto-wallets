<?php

namespace O21\CryptoWallets\Web3;

use Illuminate\Support\Arr;
use Web3\Contract;

class EthAbi
{
    protected array $eventInputs;

    public function __construct(
        protected Contract $contract
    ) {
        foreach ($contract->getEvents() as $event) {
            $this->addEventInputs($event);
        }
    }

    public function decodeEventLogData(string $data, array $topics): array
    {
        $eventName = first($topics);

        $paramNames = Arr::get($this->eventInputs, "$eventName.param_names", []);
        $paramTypes = Arr::get($this->eventInputs, "$eventName.param_types", []);
        $indexedParamNames = Arr::get($this->eventInputs, "$eventName.indexed_param_names", []);
        $indexedParamTypes = Arr::get($this->eventInputs, "$eventName.indexed_param_types", []);

        $contractAbi = $this->contract->getEthabi();

        $decodedData = array_combine(
            $paramNames,
            $contractAbi->decodeParameters(
                $paramTypes,
                $data
            )
        );

        foreach ($indexedParamNames as $key => $indexedParamName) {
            $paramType = $indexedParamTypes[$key];
            // can't decode indexed string param
            // https://stackoverflow.com/questions/73232215/how-to-decode-the-indexed-string-param-in-an-event-using-web3-js
            if ($paramType === 'string') {
                $decodedData[$indexedParamName] = Arr::get($topics, $key + 1);
            } else {
                $decodedData[$indexedParamName] = $contractAbi->decodeParameter(
                    $paramType,
                    Arr::get($topics, $key + 1)
                );
            }
        }

        return $decodedData;
    }

    protected function addEventInputs(array $event): void
    {
        $signature = $this->contract->getEthabi()->encodeEventSignature($event);

        foreach ($event['inputs'] as $input) {
            if ($input['indexed']) {
                $this->eventInputs[$signature]['indexed_param_names'][] = $input['name'];
                $this->eventInputs[$signature]['indexed_param_types'][] = $input['type'];
            } else {
                $this->eventInputs[$signature]['param_names'][] = $input['name'];
                $this->eventInputs[$signature]['param_types'][] = $input['type'];
            }
        }
    }
}
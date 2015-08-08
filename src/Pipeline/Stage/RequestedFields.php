<?php

namespace Refinery29\Piston\Pipeline\Stage;

use League\Pipeline\StageInterface;
use Refinery29\Piston\Http\Request;

/**
 * Class Fields
 */
class RequestedFields implements StageInterface
{
    use GetOnlyStage;
    /**
     * @param Request $request
     *
     * @return Request
     */
    public function process($request)
    {
        $fields = $request->get('fields');

        if ($fields) {
            $this->ensureGetOnlyRequest($request);
        }

        if ($fields) {
            $fields = explode(',', $fields);
        }

        if (!empty($fields)) {
            $request->setRequestedFields((array) $fields);
        }

        return $request;
    }
}
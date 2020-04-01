<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Util;

class ApiLogAnonymizer
{
    /**
     * Regular expressions for preg_replace
     *
     * @var string[]
     */
    private $patterns;

    /**
     * @var string
     */
    private $replacement;

    public function __construct(array $patterns = [], string $replacement = '[hidden]')
    {
        $this->patterns = $patterns;
        $this->replacement = $replacement;
    }

    /**
     * Strip sensitive strings from message by given property names.
     *
     * @param string $message
     * @return string
     */
    public function anonymize($message)
    {
        return preg_replace_callback(
            $this->patterns,
            function ($matches) {
                $result = $matches[0];
                $found = count($matches);

                // exact search
                if ($found === 1) {
                    return $this->replacement;
                }

                // search with captured sub-patterns
                for ($i = $found; $i > 1; $i--) {
                    $result = str_replace($matches[$i-1], $this->replacement, $result);
                }

                return $result;
            },
            $message
        );
    }

    /**
     * Processor for Monolog log records.
     *
     * @param mixed[] $record
     * @return mixed[]
     */
    public function __invoke(array $record)
    {
        $record['message'] = $this->anonymize($record['message']);

        return $record;
    }
}

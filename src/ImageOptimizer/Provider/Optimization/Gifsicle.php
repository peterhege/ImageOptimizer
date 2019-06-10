<?php

namespace ImageOptimizer\Provider\Optimization;

use Exception;

/**
 * Class Gifsicle
 *
 * @package ImageOptimizer\Provider\Optimization
 */
class Gifsicle extends AbstractOptimizationProvider
{
    /**
     * Gifsicle constructor.
     *
     * @param string $binaryPath
     */
    public function __construct($binaryPath)
    {
        parent::__construct($binaryPath);
    }

    /**
     * Optimize gif
     *
     * @param mixed $image
     *
     * @return string
     *
     * @throws Exception
     */
    public function optimize($image)
    {
        $cache = CACHE . "/img/" . MD5($image . time());
        $content = shell_exec($this->binaryPath . ' -O2 ' . escapeshellarg($image) . ' > ' . escapeshellarg($cache));
        $content = file_get_contents($cache);
        unlink($cache);

        if (!$content) {
            throw new Exception('There was an error during the optimization');
        }

        return $content;
    }
}

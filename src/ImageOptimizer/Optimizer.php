<?php

namespace ImageOptimizer;

use Exception;
use ImageOptimizer\Provider\Optimization\Gifsicle;
use ImageOptimizer\Provider\Optimization\Mozjpeg;
use ImageOptimizer\Provider\Optimization\Pngquant;

/**
 * Class Optimizer
 *
 * @package ImageOptimizer
 */
class Optimizer
{
    const PNGQUANT_PATH = 'pngquant';
    const MOZJPEG_PATH  = 'mozjpeg';
    const GIFSICLE_PATH = 'gifsicle';

    protected $pngquant;
    protected $mozjpeg;
    protected $gifsicle;

    /**
     * Optimizer constructor.
     *
     * @param array $optimizer
     *
     * @throws Exception
     */
    public function __construct($optimizer = array())
    {
        if (! isset($optimizer[self::PNGQUANT_PATH]) ||
            ! isset($optimizer[self::MOZJPEG_PATH]) ||
            ! isset($optimizer[self::GIFSICLE_PATH])
        ) {
            throw new Exception('Optimizer not set');
        }

        if (! file_exists($optimizer[self::PNGQUANT_PATH]) ||
            ! file_exists($optimizer[self::MOZJPEG_PATH]) ||
            ! file_exists($optimizer[self::GIFSICLE_PATH])
        ) {
            throw new Exception('Optimizer not found');
        }
        
        if (!defined('IMAGE_OPTIMIZER_CACHE')) {
            define('IMAGE_OPTIMIZER_CACHE', __DIR__ . "/cache");
        }
        if (!file_exists(IMAGE_OPTIMIZER_CACHE . "/img")) {
            mkdir(IMAGE_OPTIMIZER_CACHE . "/img", 0755, true);
        }

        $this->mozjpeg  = new Mozjpeg($optimizer[self::MOZJPEG_PATH]);
        $this->pngquant = new Pngquant($optimizer[self::PNGQUANT_PATH]);
        $this->gifsicle = new Gifsicle($optimizer[self::GIFSICLE_PATH]);
    }

    /**
     * Optimize image
     *
     * @param string $input
     * @param string $output
     *
     * @return bool
     *
     * @throws Exception
     */
    public function optimize($input, $output = '')
    {
        $ext = exif_imagetype($input);
        switch ($ext) {
            case IMAGETYPE_JPEG:
                $content = $this->mozjpeg->optimize($input);
                break;
            case IMAGETYPE_PNG:
                $content = $this->pngquant->optimize($input);
                break;
            case IMAGETYPE_GIF:
                $content = $this->gifsicle->optimize($input);
                break;
            default:
                throw new Exception('No valid file type');
        }

        if (! empty($output)) {
            $result = file_put_contents($output, $content);
        } else {
            $result = file_put_contents($input, $content);
        }

        if (false === $result) {
            throw new Exception('Could not write to file');
        }

        return true;
    }
}

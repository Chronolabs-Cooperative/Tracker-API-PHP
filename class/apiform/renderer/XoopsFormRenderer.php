<?php
/**
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */

/**
 * Factory to build handlers
 *
 * @category  APIForm
 * @package   APIFormRenderer
 * @author    Richard Griffith <richard@geekwright.com>
 * @copyright 2017 API Project (http://api.org)
 * @license   GNU GPL 2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 * @link      http://api.org
 */
class APIFormRenderer
{
    /**
     * @var APIFormRenderer The reference to *Singleton* instance of this class
     */
    private static $instance;

    /**
     * @var APIFormRendererInterface The reference to *Singleton* instance of this class
     */
    protected $renderer;

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return APIFormRenderer the singleton instance.
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct()
    {
    }

    /**
     * Private clone method to prevent cloning of the instance of the
     * *Singleton* instance.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Private unserialize method to prevent unserializing of the *Singleton*
     * instance.
     *
     * @return void
     */
    private function __wakeup()
    {
    }

    /**
     * set the renderer
     *
     * @param APIFormRendererInterface $renderer instance of renderer
     *
     * @return void
     */
    public function set(APIFormRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * get the renderer
     *
     * @return APIFormRendererInterface
     */
    public function get()
    {
        // return a default if not set
        if (null === $this->renderer) {
            api_load('apiformrendererlegacy');
            $this->renderer = new APIFormRendererLegacy();
        }

        return $this->renderer;
    }
}

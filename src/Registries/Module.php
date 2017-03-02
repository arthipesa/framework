<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

namespace O2System\Framework\Registries;

// ------------------------------------------------------------------------

use O2System\Framework\Registries\Module\Theme;
use O2System\Spl\Datastructures\SplArrayObject;
use O2System\Spl\Info\SplDirectoryInfo;

/**
 * Class Module
 * @package O2System\Framework\Registries
 */
class Module extends SplDirectoryInfo
{
    private $type = 'MODULE';

    /**
     * Module Namespace
     *
     * @var string
     */
    private $namespace;

    /**
     * Module Segments
     *
     * @var string
     */
    private $segments;

    /**
     * Module Parent Segments
     *
     * @var string
     */
    private $parentSegments;

    /**
     * Module Properties
     *
     * @var array
     */
    private $properties = [ ];

    /**
     * Module Config
     *
     * @var array
     */
    private $config = [ ];

    public function __construct ( $dir )
    {
        parent::__construct( $dir );

        $this->namespace = prepare_namespace( str_replace( PATH_ROOT, '', $dir ), false );
    }

    public function getType ()
    {
        return $this->type;
    }

    public function setType ( $type )
    {
        $this->type = strtoupper( $type );

        return $this;
    }

    public function setSegments ( $segments )
    {
        $this->segments = is_array( $segments ) ? implode( '/', $segments ) : $segments;

        return $this;
    }

    public function setParentSegments ( $parentSegments )
    {
        $this->parentSegments = is_array( $parentSegments ) ? implode( '/', $parentSegments ) : $parentSegments;

        return $this;
    }

    public function getSegments( $returnArray = true )
    {
        if( $returnArray ) {
            return explode( '/', $this->segments );
        }

        return $this->segments;
    }

    public function getParentSegments( $returnArray = true )
    {
        if( $returnArray ) {
            return explode( '/', $this->parentSegments );
        }

        return $this->parentSegments;
    }

    public function getParameter ()
    {
        return strtolower( $this->getDirName() );
    }

    public function getCode ()
    {
        return strtoupper( substr( md5( $this->getDirName() ), 2, 7 ) );
    }

    public function getChecksum ()
    {
        return md5( $this->getMTime() );
    }

    public function getProperties ()
    {
        return new SplArrayObject( $this->properties );
    }

    public function setProperties ( array $properties )
    {
        $this->properties = $properties;

        return $this;
    }

    public function getConfig ()
    {
        return new SplArrayObject( $this->config );
    }

    public function setConfig ( array $config )
    {
        $this->config = $config;

        return $this;
    }

    public function getNamespace ()
    {
        return $this->namespace;
    }

    public function setNamespace ( $namespace )
    {
        $this->namespace = trim( $namespace, '\\' ) . '\\';

        return $this;
    }

    public function getTheme ( $theme, $failover = true )
    {
        $theme = dash( $theme );

        if ( $failover === false ) {
            if ( is_dir( $themePath = $this->getThemesPath() . $theme . DIRECTORY_SEPARATOR ) ) {
                $themeObject = new Theme( $themePath );

                if ( $themeObject->isValid() ) {
                    return $themeObject;
                }
            }
        } else {
            foreach ( modules() as $module ) {
                if ( in_array( $module->getType(), [ 'KERNEL', 'FRAMEWORK' ] ) ) {
                    continue;
                } elseif ( $themeObject = $module->getTheme( $theme, false ) ) {
                    return $themeObject;
                }
            }
        }

        return false;
    }

    public function getDir( $dirname, $psrDir = false )
    {
        $dirname = $psrDir === true ? prepare_class_name( $dirname ) : $dirname;
        $dirname = str_replace( [ '/', '\\' ], DIRECTORY_SEPARATOR, $dirname );

        if( is_dir( $dirpath = $this->getRealPath() . $dirname ) ) {
            return $dirpath . DIRECTORY_SEPARATOR;
        }

        return false;
    }

    public function getThemesPath ()
    {
        return str_replace( PATH_APP, PATH_PUBLIC, $this->getRealPath() ) . 'themes' . DIRECTORY_SEPARATOR;
    }

    public function hasTheme ( $theme )
    {
        if ( is_dir( $this->getThemesPath() . $theme ) ) {
            return true;
        }

        return false;
    }

    public function loadModel ()
    {
        $modelClassName = $this->namespace . 'Base\\Model';

        if ( class_exists( $modelClassName ) ) {
            models()->register( strtolower( $this->type ), new $modelClassName() );
        }
    }
}
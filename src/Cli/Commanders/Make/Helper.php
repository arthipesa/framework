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

namespace O2System\Framework\Cli\Commanders\Make;

// ------------------------------------------------------------------------

use O2System\Framework\Cli\Commanders\Make;
use O2System\Kernel\Cli\Writers\Format;

/**
 * Class Helper
 *
 * @package O2System\Framework\Cli\Commanders\Make
 */
class Helper extends Make
{
    /**
     * Helper::$commandDescription
     *
     * Command description.
     *
     * @var string
     */
    protected $commandDescription = 'DESC_CLI_MAKE_HELPER';

    public function execute()
    {
        parent::execute();

        if ( empty( $this->optionFilename ) ) {
            output()->write(
                ( new Format() )
                    ->setContextualClass( Format::DANGER )
                    ->setString( language()->getLine( 'E_CLI_HELPER_FILENAME' ) )
                    ->setNewLinesAfter( 1 )
            );

            exit( EXIT_ERROR );
        }

        if ( strpos( $this->optionPath, 'Helpers' ) === false ) {
            $filePath = $this->optionPath . 'Helpers' . DIRECTORY_SEPARATOR . $this->optionFilename;
        } else {
            $filePath = $this->optionPath . $this->optionFilename;
        }

        if ( ! is_dir( dirname( $filePath ) ) ) {
            mkdir( dirname( $filePath ), 777, true );
        }

        if ( is_file( $filePath ) ) {
            output()->write(
                ( new Format() )
                    ->setContextualClass( Format::DANGER )
                    ->setString( language()->getLine( 'E_CLI_HELPER_EXISTS', [ $filePath ] ) )
                    ->setNewLinesAfter( 1 )
            );

            exit( EXIT_ERROR );
        }

        $vars[ 'CREATE_DATETIME' ] = date( 'd/m/Y H:m' );
        $vars[ 'HELPER' ] = underscore(
            decamelcase(
                pathinfo( $filePath, PATHINFO_FILENAME )
            )
        );
        $vars[ 'FILEPATH' ] = $filePath;

        $phpTemplate = <<<PHPTEMPLATE
<?php
/**
 * Created by O2System Framework File Generator.
 * DateTime: CREATE_DATETIME
 */

// ------------------------------------------------------------------------

if ( ! function_exists( 'HELPER' ) ) {
    /**
     * HELPER
     */
    function HELPER() {
    }
}
PHPTEMPLATE;

        $fileContent = str_replace( array_keys( $vars ), array_values( $vars ), $phpTemplate );
        file_put_contents( $filePath, $fileContent );

        if ( is_file( $filePath ) ) {
            output()->write(
                ( new Format() )
                    ->setContextualClass( Format::SUCCESS )
                    ->setString( language()->getLine( 'S_CLI_HELPER_MAKE', [ $filePath ] ) )
                    ->setNewLinesAfter( 1 )
            );

            exit( EXIT_SUCCESS );
        }
    }
}
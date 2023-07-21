<?php

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Cookie;

use DateTimeInterface;

/**
 * Interface for a fresh Cookie instance with selected attribute(s)
 * only changed from the original instance.
 */
interface CloneableCookieInterface extends CookieInterface
{
    /**
     * Creates a new Cookie with a new cookie prefix.
     *
     * @return static
     */
    public function withPrefix(string $prefix = '');

    /**
     * Creates a new Cookie with a new name.
     *
     * @return static
     */
    public function withName(string $name);

    /**
     * Creates a new Cookie with new value.
     *
     * @return static
     */
    public function withValue(string $value);

    /**
     * Creates a new Cookie with a new cookie expires time.
     *
     * @param DateTimeInterface|int|string $expires
     *
     * @return static
     */
    public function withExpires($expires);

    /**
     * Creates a new Cookie that will expire the cookie from the browser.
     *
     * @return static
     */
    public function withExpired();

    /**
     * Creates a new Cookie that will virtually never expire from the browser.
     *
     * @return static
     *
     * @deprecated See https://github.com/codeigniter4/CodeIgniter4/pull/6413
     */
    public function withNeverExpiring();

    /**
     * Creates a new Cookie with a new path on the server the cookie is available.
     *
     * @return static
     */
    public function withPath(?string $path);

    /**
     * Creates a new Cookie with a new domain the cookie is available.
     *
     * @return static
     */
    public function withDomain(?string $domain);

    /**
     * Creates a new Cookie with a new "Secure" attribute.
     *
     * @return static
     */
    public function withSecure(bool $secure = true);

    /**
     * Creates a new Cookie with a new "HttpOnly" attribute
     *
     * @return static
     */
    public function withHTTPOnly(bool $httponly = true);

    /**
     * Creates a new Cookie with a new "SameSite" attribute.
     *
     * @return static
     */
    public function withSameSite(string $samesite);

    /**
     * Creates a new Cookie with URL encoding option updated.
     *
     * @return static
     */
    public function withRaw(bool $raw = true);
}

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Commands\Generators;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\CLI\GeneratorTrait;
use Config\App;
use Config\Migrations;

/**
 * Generates a migration file for database sessions.
 *
 * @deprecated Use `make:migration --session` instead.
 *
 * @codeCoverageIgnore
 */
class SessionMigrationGenerator extends BaseCommand
{
    use GeneratorTrait;

    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Generators';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'session:migration';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = '[DEPRECATED] Generates the migration file for database sessions, Please use  "make:migration --session" instead.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'session:migration [options]';

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '-t' => 'Supply a table name.',
        '-g' => 'Database group to use. Default: "default".',
    ];

    /**
     * Actually execute a command.
     */
    public function run(array $params)
    {
        $this->component = 'Migration';
        $this->directory = 'Database\Migrations';
        $this->template  = 'migration.tpl.php';

        $table = 'ci_sessions';

        if (array_key_exists('t', $params) || CLI::getOption('t')) {
            $table = $params['t'] ?? CLI::getOption('t');
        }

        $params[0] = "_create_{$table}_table";

        $this->execute($params);
    }

    /**
     * Performs the necessary replacements.
     */
    protected function prepare(string $class): string
    {
        $data            = [];
        $data['session'] = true;
        $data['table']   = $this->getOption('t');
        $data['DBGroup'] = $this->getOption('g');
        $data['matchIP'] = config(App::class)->sessionMatchIP ?? false;

        $data['table']   = is_string($data['table']) ? $data['table'] : 'ci_sessions';
        $data['DBGroup'] = is_string($data['DBGroup']) ? $data['DBGroup'] : 'default';

        return $this->parseTemplate($class, [], [], $data);
    }

    /**
     * Change file basename before saving.
     */
    protected function basename(string $filename): string
    {
        return gmdate(config(Migrations::class)->timestampFormat) . basename($filename);
    }
}

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Commands\Generators;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\GeneratorTrait;

/**
 * Generates a skeleton seeder file.
 */
class SeederGenerator extends BaseCommand
{
    use GeneratorTrait;

    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Generators';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'make:seeder';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Generates a new seeder file.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'make:seeder <name> [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'name' => 'The seeder class name.',
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--namespace' => 'Set root namespace. Default: "APP_NAMESPACE".',
        '--suffix'    => 'Append the component title to the class name (e.g. User => UserSeeder).',
        '--force'     => 'Force overwrite existing file.',
    ];

    /**
     * Actually execute a command.
     */
    public function run(array $params)
    {
        $this->component = 'Seeder';
        $this->directory = 'Database\Seeds';
        $this->template  = 'seeder.tpl.php';

        $this->classNameLang = 'CLI.generator.className.seeder';
        $this->execute($params);
    }
}

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Commands\Generators;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\GeneratorTrait;

/**
 * Generates a skeleton Entity file.
 */
class EntityGenerator extends BaseCommand
{
    use GeneratorTrait;

    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Generators';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'make:entity';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Generates a new entity file.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'make:entity <name> [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'name' => 'The entity class name.',
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--namespace' => 'Set root namespace. Default: "APP_NAMESPACE".',
        '--suffix'    => 'Append the component title to the class name (e.g. User => UserEntity).',
        '--force'     => 'Force overwrite existing file.',
    ];

    /**
     * Actually execute a command.
     */
    public function run(array $params)
    {
        $this->component = 'Entity';
        $this->directory = 'Entities';
        $this->template  = 'entity.tpl.php';

        $this->classNameLang = 'CLI.generator.className.entity';
        $this->execute($params);
    }
}

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Commands\Generators;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use CodeIgniter\CLI\GeneratorTrait;
use CodeIgniter\Controller;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\RESTful\ResourcePresenter;

/**
 * Generates a skeleton controller file.
 */
class ControllerGenerator extends BaseCommand
{
    use GeneratorTrait;

    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Generators';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'make:controller';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Generates a new controller file.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'make:controller <name> [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'name' => 'The controller class name.',
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--bare'      => 'Extends from CodeIgniter\Controller instead of BaseController.',
        '--restful'   => 'Extends from a RESTful resource, Options: [controller, presenter]. Default: "controller".',
        '--namespace' => 'Set root namespace. Default: "APP_NAMESPACE".',
        '--suffix'    => 'Append the component title to the class name (e.g. User => UserController).',
        '--force'     => 'Force overwrite existing file.',
    ];

    /**
     * Actually execute a command.
     */
    public function run(array $params)
    {
        $this->component = 'Controller';
        $this->directory = 'Controllers';
        $this->template  = 'controller.tpl.php';

        $this->classNameLang = 'CLI.generator.className.controller';
        $this->execute($params);
    }

    /**
     * Prepare options and do the necessary replacements.
     */
    protected function prepare(string $class): string
    {
        $bare = $this->getOption('bare');
        $rest = $this->getOption('restful');

        $useStatement = trim(APP_NAMESPACE, '\\') . '\Controllers\BaseController';
        $extends      = 'BaseController';

        // Gets the appropriate parent class to extend.
        if ($bare || $rest) {
            if ($bare) {
                $useStatement = Controller::class;
                $extends      = 'Controller';
            } elseif ($rest) {
                $rest = is_string($rest) ? $rest : 'controller';

                if (! in_array($rest, ['controller', 'presenter'], true)) {
                    // @codeCoverageIgnoreStart
                    $rest = CLI::prompt(lang('CLI.generator.parentClass'), ['controller', 'presenter'], 'required');
                    CLI::newLine();
                    // @codeCoverageIgnoreEnd
                }

                if ($rest === 'controller') {
                    $useStatement = ResourceController::class;
                    $extends      = 'ResourceController';
                } elseif ($rest === 'presenter') {
                    $useStatement = ResourcePresenter::class;
                    $extends      = 'ResourcePresenter';
                }
            }
        }

        return $this->parseTemplate(
            $class,
            ['{useStatement}', '{extends}'],
            [$useStatement, $extends],
            ['type' => $rest]
        );
    }
}

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\Commands\Generators;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\GeneratorTrait;

/**
 * Generates a skeleton Cell and its view.
 */
class CellGenerator extends BaseCommand
{
    use GeneratorTrait;

    /**
     * The Command's Group
     *
     * @var string
     */
    protected $group = 'Generators';

    /**
     * The Command's Name
     *
     * @var string
     */
    protected $name = 'make:cell';

    /**
     * The Command's Description
     *
     * @var string
     */
    protected $description = 'Generates a new Cell file and its view.';

    /**
     * The Command's Usage
     *
     * @var string
     */
    protected $usage = 'make:cell <name> [options]';

    /**
     * The Command's Arguments
     *
     * @var array
     */
    protected $arguments = [
        'name' => 'The cell class name.',
    ];

    /**
     * The Command's Options
     *
     * @var array
     */
    protected $options = [
        '--namespace' => 'Set root namespace. Default: "APP_NAMESPACE".',
        '--force'     => 'Force overwrite existing file.',
    ];

    /**
     * Actually execute a command.
     */
    public function run(array $params)
    {
        $this->component = 'Cell';
        $this->directory = 'Cells';

        $params = array_merge($params, ['suffix' => null]);

        $this->template      = 'cell.tpl.php';
        $this->classNameLang = 'CLI.generator.className.cell';
        $this->generateClass($params);

        $this->name          = 'make:cell_view';
        $this->template      = 'cell_view.tpl.php';
        $this->classNameLang = 'CLI.generator.viewName.cell';

        $className = $this->qualifyClassName();
        $viewName  = decamelize(class_basename($className));
        $viewName  = preg_replace('/([a-z][a-z0-9_\/\\\\]+)(_cell)$/i', '$1', $viewName) ?? $viewName;
        $namespace = substr($className, 0, strrpos($className, '\\') + 1);

        $this->generateView($namespace . $viewName, $params);

        return 0;
    }
}

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\API;

use CodeIgniter\Format\FormatterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;

/**
 * Provides common, more readable, methods to provide
 * consistent HTTP responses under a variety of common
 * situations when working as an API.
 */
trait ResponseTrait
{
    /**
     * Allows child classes to override the
     * status code that is used in their API.
     *
     * @var array<string, int>
     */
    protected $codes = [
        'created'                   => 201,
        'deleted'                   => 200,
        'updated'                   => 200,
        'no_content'                => 204,
        'invalid_request'           => 400,
        'unsupported_response_type' => 400,
        'invalid_scope'             => 400,
        'temporarily_unavailable'   => 400,
        'invalid_grant'             => 400,
        'invalid_credentials'       => 400,
        'invalid_refresh'           => 400,
        'no_data'                   => 400,
        'invalid_data'              => 400,
        'access_denied'             => 401,
        'unauthorized'              => 401,
        'invalid_client'            => 401,
        'forbidden'                 => 403,
        'resource_not_found'        => 404,
        'not_acceptable'            => 406,
        'resource_exists'           => 409,
        'conflict'                  => 409,
        'resource_gone'             => 410,
        'payload_too_large'         => 413,
        'unsupported_media_type'    => 415,
        'too_many_requests'         => 429,
        'server_error'              => 500,
        'unsupported_grant_type'    => 501,
        'not_implemented'           => 501,
    ];

    /**
     * How to format the response data.
     * Either 'json' or 'xml'. If blank will be
     * determined through content negotiation.
     *
     * @var string
     */
    protected $format = 'json';

    /**
     * Current Formatter instance. This is usually set by ResponseTrait::format
     *
     * @var FormatterInterface|null
     */
    protected $formatter;

    /**
     * Provides a single, simple method to return an API response, formatted
     * to match the requested format, with proper content-type and status code.
     *
     * @param array|string|null $data
     *
     * @return ResponseInterface
     */
    protected function respond($data = null, ?int $status = null, string $message = '')
    {
        if ($data === null && $status === null) {
            $status = 404;
            $output = null;
            $this->format($data);
        } elseif ($data === null && is_numeric($status)) {
            $output = null;
            $this->format($data);
        } else {
            $status = empty($status) ? 200 : $status;
            $output = $this->format($data);
        }

        if ($output !== null) {
            if ($this->format === 'json') {
                return $this->response->setJSON($output)->setStatusCode($status, $message);
            }

            if ($this->format === 'xml') {
                return $this->response->setXML($output)->setStatusCode($status, $message);
            }
        }

        return $this->response->setBody($output)->setStatusCode($status, $message);
    }

    /**
     * Used for generic failures that no custom methods exist for.
     *
     * @param array|string $messages
     * @param int          $status   HTTP status code
     * @param string|null  $code     Custom, API-specific, error code
     *
     * @return ResponseInterface
     */
    protected function fail($messages, int $status = 400, ?string $code = null, string $customMessage = '')
    {
        if (! is_array($messages)) {
            $messages = ['error' => $messages];
        }

        $response = [
            'status'   => $status,
            'error'    => $code ?? $status,
            'messages' => $messages,
        ];

        return $this->respond($response, $status, $customMessage);
    }

    // --------------------------------------------------------------------
    // Response Helpers
    // --------------------------------------------------------------------

    /**
     * Used after successfully creating a new resource.
     *
     * @param array|string|null $data
     *
     * @return ResponseInterface
     */
    protected function respondCreated($data = null, string $message = '')
    {
        return $this->respond($data, $this->codes['created'], $message);
    }

    /**
     * Used after a resource has been successfully deleted.
     *
     * @param array|string|null $data
     *
     * @return ResponseInterface
     */
    protected function respondDeleted($data = null, string $message = '')
    {
        return $this->respond($data, $this->codes['deleted'], $message);
    }

    /**
     * Used after a resource has been successfully updated.
     *
     * @param array|string|null $data
     *
     * @return ResponseInterface
     */
    protected function respondUpdated($data = null, string $message = '')
    {
        return $this->respond($data, $this->codes['updated'], $message);
    }

    /**
     * Used after a command has been successfully executed but there is no
     * meaningful reply to send back to the client.
     *
     * @return ResponseInterface
     */
    protected function respondNoContent(string $message = 'No Content')
    {
        return $this->respond(null, $this->codes['no_content'], $message);
    }

    /**
     * Used when the client is either didn't send authorization information,
     * or had bad authorization credentials. User is encouraged to try again
     * with the proper information.
     *
     * @return ResponseInterface
     */
    protected function failUnauthorized(string $description = 'Unauthorized', ?string $code = null, string $message = '')
    {
        return $this->fail($description, $this->codes['unauthorized'], $code, $message);
    }

    /**
     * Used when access is always denied to this resource and no amount
     * of trying again will help.
     *
     * @return ResponseInterface
     */
    protected function failForbidden(string $description = 'Forbidden', ?string $code = null, string $message = '')
    {
        return $this->fail($description, $this->codes['forbidden'], $code, $message);
    }

    /**
     * Used when a specified resource cannot be found.
     *
     * @return ResponseInterface
     */
    protected function failNotFound(string $description = 'Not Found', ?string $code = null, string $message = '')
    {
        return $this->fail($description, $this->codes['resource_not_found'], $code, $message);
    }

    /**
     * Used when the data provided by the client cannot be validated.
     *
     * @return ResponseInterface
     *
     * @deprecated Use failValidationErrors instead
     */
    protected function failValidationError(string $description = 'Bad Request', ?string $code = null, string $message = '')
    {
        return $this->fail($description, $this->codes['invalid_data'], $code, $message);
    }

    /**
     * Used when the data provided by the client cannot be validated on one or more fields.
     *
     * @param string|string[] $errors
     *
     * @return ResponseInterface
     */
    protected function failValidationErrors($errors, ?string $code = null, string $message = '')
    {
        return $this->fail($errors, $this->codes['invalid_data'], $code, $message);
    }

    /**
     * Use when trying to create a new resource and it already exists.
     *
     * @return ResponseInterface
     */
    protected function failResourceExists(string $description = 'Conflict', ?string $code = null, string $message = '')
    {
        return $this->fail($description, $this->codes['resource_exists'], $code, $message);
    }

    /**
     * Use when a resource was previously deleted. This is different than
     * Not Found, because here we know the data previously existed, but is now gone,
     * where Not Found means we simply cannot find any information about it.
     *
     * @return ResponseInterface
     */
    protected function failResourceGone(string $description = 'Gone', ?string $code = null, string $message = '')
    {
        return $this->fail($description, $this->codes['resource_gone'], $code, $message);
    }

    /**
     * Used when the user has made too many requests for the resource recently.
     *
     * @return ResponseInterface
     */
    protected function failTooManyRequests(string $description = 'Too Many Requests', ?string $code = null, string $message = '')
    {
        return $this->fail($description, $this->codes['too_many_requests'], $code, $message);
    }

    /**
     * Used when there is a server error.
     *
     * @param string      $description The error message to show the user.
     * @param string|null $code        A custom, API-specific, error code.
     * @param string      $message     A custom "reason" message to return.
     */
    protected function failServerError(string $description = 'Internal Server Error', ?string $code = null, string $message = ''): ResponseInterface
    {
        return $this->fail($description, $this->codes['server_error'], $code, $message);
    }

    // --------------------------------------------------------------------
    // Utility Methods
    // --------------------------------------------------------------------

    /**
     * Handles formatting a response. Currently makes some heavy assumptions
     * and needs updating! :)
     *
     * @param array|string|null $data
     *
     * @return string|null
     */
    protected function format($data = null)
    {
        // If the data is a string, there's not much we can do to it...
        if (is_string($data)) {
            // The content type should be text/... and not application/...
            $contentType = $this->response->getHeaderLine('Content-Type');
            $contentType = str_replace('application/json', 'text/html', $contentType);
            $contentType = str_replace('application/', 'text/', $contentType);
            $this->response->setContentType($contentType);
            $this->format = 'html';

            return $data;
        }

        $format = Services::format();
        $mime   = "application/{$this->format}";

        // Determine correct response type through content negotiation if not explicitly declared
        if (
            (empty($this->format) || ! in_array($this->format, ['json', 'xml'], true))
            && $this->request instanceof IncomingRequest
        ) {
            $mime = $this->request->negotiate(
                'media',
                $format->getConfig()->supportedResponseFormats,
                false
            );
        }

        $this->response->setContentType($mime);

        // if we don't have a formatter, make one
        if (! isset($this->formatter)) {
            // if no formatter, use the default
            $this->formatter = $format->getFormatter($mime);
        }

        if ($mime !== 'application/json') {
            // Recursively convert objects into associative arrays
            // Conversion not required for JSONFormatter
            $data = json_decode(json_encode($data), true);
        }

        return $this->formatter->format($data);
    }

    /**
     * Sets the format the response should be in.
     *
     * @return $this
     */
    protected function setResponseFormat(?string $format = null)
    {
        $this->format = strtolower($format);

        return $this;
    }
}


/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\CLI;

use Config\Exceptions;
use Psr\Log\LoggerInterface;
use ReflectionException;
use Throwable;

/**
 * BaseCommand is the base class used in creating CLI commands.
 *
 * @property array           $arguments
 * @property Commands        $commands
 * @property string          $description
 * @property string          $group
 * @property LoggerInterface $logger
 * @property string          $name
 * @property array           $options
 * @property string          $usage
 */
abstract class BaseCommand
{
    /**
     * The group the command is lumped under
     * when listing commands.
     *
     * @var string
     */
    protected $group;

    /**
     * The Command's name
     *
     * @var string
     */
    protected $name;

    /**
     * the Command's usage description
     *
     * @var string
     */
    protected $usage;

    /**
     * the Command's short description
     *
     * @var string
     */
    protected $description;

    /**
     * the Command's options description
     *
     * @var array
     */
    protected $options = [];

    /**
     * the Command's Arguments description
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * The Logger to use for a command
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Instance of Commands so
     * commands can call other commands.
     *
     * @var Commands
     */
    protected $commands;

    public function __construct(LoggerInterface $logger, Commands $commands)
    {
        $this->logger   = $logger;
        $this->commands = $commands;
    }

    /**
     * Actually execute a command.
     *
     * @param array<int|string, string|null> $params
     *
     * @return int|void
     */
    abstract public function run(array $params);

    /**
     * Can be used by a command to run other commands.
     *
     * @return int|void
     *
     * @throws ReflectionException
     */
    protected function call(string $command, array $params = [])
    {
        return $this->commands->run($command, $params);
    }

    /**
     * A simple method to display an error with line/file, in child commands.
     */
    protected function showError(Throwable $e)
    {
        $exception = $e;
        $message   = $e->getMessage();
        $config    = config(Exceptions::class);

        require $config->errorViewPath . '/cli/error_exception.php';
    }

    /**
     * Show Help includes (Usage, Arguments, Description, Options).
     */
    public function showHelp()
    {
        CLI::write(lang('CLI.helpUsage'), 'yellow');

        if (! empty($this->usage)) {
            $usage = $this->usage;
        } else {
            $usage = $this->name;

            if (! empty($this->arguments)) {
                $usage .= ' [arguments]';
            }
        }

        CLI::write($this->setPad($usage, 0, 0, 2));

        if (! empty($this->description)) {
            CLI::newLine();
            CLI::write(lang('CLI.helpDescription'), 'yellow');
            CLI::write($this->setPad($this->description, 0, 0, 2));
        }

        if (! empty($this->arguments)) {
            CLI::newLine();
            CLI::write(lang('CLI.helpArguments'), 'yellow');
            $length = max(array_map('strlen', array_keys($this->arguments)));

            foreach ($this->arguments as $argument => $description) {
                CLI::write(CLI::color($this->setPad($argument, $length, 2, 2), 'green') . $description);
            }
        }

        if (! empty($this->options)) {
            CLI::newLine();
            CLI::write(lang('CLI.helpOptions'), 'yellow');
            $length = max(array_map('strlen', array_keys($this->options)));

            foreach ($this->options as $option => $description) {
                CLI::write(CLI::color($this->setPad($option, $length, 2, 2), 'green') . $description);
            }
        }
    }

    /**
     * Pads our string out so that all titles are the same length to nicely line up descriptions.
     *
     * @param int $extra How many extra spaces to add at the end
     */
    public function setPad(string $item, int $max, int $extra = 2, int $indent = 0): string
    {
        $max += $extra + $indent;

        return str_pad(str_repeat(' ', $indent) . $item, $max);
    }

    /**
     * Get pad for $key => $value array output
     *
     * @deprecated Use setPad() instead.
     *
     * @codeCoverageIgnore
     */
    public function getPad(array $array, int $pad): int
    {
        $max = 0;

        foreach (array_keys($array) as $key) {
            $max = max($max, strlen($key));
        }

        return $max + $pad;
    }

    /**
     * Makes it simple to access our protected properties.
     *
     * @return array|Commands|LoggerInterface|string|null
     */
    public function __get(string $key)
    {
        return $this->{$key} ?? null;
    }

    /**
     * Makes it simple to check our protected properties.
     */
    public function __isset(string $key): bool
    {
        return isset($this->{$key});
    }
}


/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace CodeIgniter\CLI;

use Config\Generators;
use Config\Services;
use Throwable;

/**
 * GeneratorTrait contains a collection of methods
 * to build the commands that generates a file.
 */
trait GeneratorTrait
{
    /**
     * Component Name
     *
     * @var string
     */
    protected $component;

    /**
     * File directory
     *
     * @var string
     */
    protected $directory;

    /**
     * View template name
     *
     * @var string
     */
    protected $template;

    /**
     * Language string key for required class names.
     *
     * @var string
     */
    protected $classNameLang = '';

    /**
     * Whether to require class name.
     *
     * @internal
     *
     * @var bool
     */
    private $hasClassName = true;

    /**
     * Whether to sort class imports.
     *
     * @internal
     *
     * @var bool
     */
    private $sortImports = true;

    /**
     * Whether the `--suffix` option has any effect.
     *
     * @internal
     *
     * @var bool
     */
    private $enabledSuffixing = true;

    /**
     * The params array for easy access by other methods.
     *
     * @internal
     *
     * @var array
     */
    private $params = [];

    /**
     * Execute the command.
     *
     * @deprecated use generateClass() instead
     */
    protected function execute(array $params): void
    {
        $this->generateClass($params);
    }

    /**
     * Generates a class file from an existing template.
     */
    protected function generateClass(array $params)
    {
        $this->params = $params;

        // Get the fully qualified class name from the input.
        $class = $this->qualifyClassName();

        // Get the file path from class name.
        $target = $this->buildPath($class);

        // Check if path is empty.
        if (empty($target)) {
            return;
        }

        $this->generateFile($target, $this->buildContent($class));
    }

    /**
     * Generate a view file from an existing template.
     */
    protected function generateView(string $view, array $params)
    {
        $this->params = $params;

        $target = $this->buildPath($view);

        // Check if path is empty.
        if (empty($target)) {
            return;
        }

        $this->generateFile($target, $this->buildContent($view));
    }

    /**
     * Handles writing the file to disk, and all of the safety checks around that.
     */
    private function generateFile(string $target, string $content): void
    {
        if ($this->getOption('namespace') === 'CodeIgniter') {
            // @codeCoverageIgnoreStart
            CLI::write(lang('CLI.generator.usingCINamespace'), 'yellow');
            CLI::newLine();

            if (CLI::prompt('Are you sure you want to continue?', ['y', 'n'], 'required') === 'n') {
                CLI::newLine();
                CLI::write(lang('CLI.generator.cancelOperation'), 'yellow');
                CLI::newLine();

                return;
            }

            CLI::newLine();
            // @codeCoverageIgnoreEnd
        }

        $isFile = is_file($target);

        // Overwriting files unknowingly is a serious annoyance, So we'll check if
        // we are duplicating things, If 'force' option is not supplied, we bail.
        if (! $this->getOption('force') && $isFile) {
            CLI::error(lang('CLI.generator.fileExist', [clean_path($target)]), 'light_gray', 'red');
            CLI::newLine();

            return;
        }

        // Check if the directory to save the file is existing.
        $dir = dirname($target);

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        helper('filesystem');

        // Build the class based on the details we have, We'll be getting our file
        // contents from the template, and then we'll do the necessary replacements.
        if (! write_file($target, $content)) {
            // @codeCoverageIgnoreStart
            CLI::error(lang('CLI.generator.fileError', [clean_path($target)]), 'light_gray', 'red');
            CLI::newLine();

            return;
            // @codeCoverageIgnoreEnd
        }

        if ($this->getOption('force') && $isFile) {
            CLI::write(lang('CLI.generator.fileOverwrite', [clean_path($target)]), 'yellow');
            CLI::newLine();

            return;
        }

        CLI::write(lang('CLI.generator.fileCreate', [clean_path($target)]), 'green');
        CLI::newLine();
    }

    /**
     * Prepare options and do the necessary replacements.
     */
    protected function prepare(string $class): string
    {
        return $this->parseTemplate($class);
    }

    /**
     * Change file basename before saving.
     *
     * Useful for components where the file name has a date.
     */
    protected function basename(string $filename): string
    {
        return basename($filename);
    }

    /**
     * Parses the class name and checks if it is already qualified.
     */
    protected function qualifyClassName(): string
    {
        // Gets the class name from input.
        $class = $this->params[0] ?? CLI::getSegment(2);

        if ($class === null && $this->hasClassName) {
            // @codeCoverageIgnoreStart
            $nameLang = $this->classNameLang ?: 'CLI.generator.className.default';
            $class    = CLI::prompt(lang($nameLang), null, 'required');
            CLI::newLine();
            // @codeCoverageIgnoreEnd
        }

        helper('inflector');

        $component = singular($this->component);

        /**
         * @see https://regex101.com/r/a5KNCR/2
         */
        $pattern = sprintf('/([a-z][a-z0-9_\/\\\\]+)(%s)$/i', $component);

        if (preg_match($pattern, $class, $matches) === 1) {
            $class = $matches[1] . ucfirst($matches[2]);
        }

        if ($this->enabledSuffixing && $this->getOption('suffix') && preg_match($pattern, $class) !== 1) {
            $class .= ucfirst($component);
        }

        // Trims input, normalize separators, and ensure that all paths are in Pascalcase.
        $class = ltrim(implode('\\', array_map('pascalize', explode('\\', str_replace('/', '\\', trim($class))))), '\\/');

        // Gets the namespace from input. Don't forget the ending backslash!
        $namespace = trim(str_replace('/', '\\', $this->getOption('namespace') ?? APP_NAMESPACE), '\\') . '\\';

        if (strncmp($class, $namespace, strlen($namespace)) === 0) {
            return $class; // @codeCoverageIgnore
        }

        return $namespace . $this->directory . '\\' . str_replace('/', '\\', $class);
    }

    /**
     * Gets the generator view as defined in the `Config\Generators::$views`,
     * with fallback to `$template` when the defined view does not exist.
     */
    protected function renderTemplate(array $data = []): string
    {
        try {
            return view(config(Generators::class)->views[$this->name], $data, ['debug' => false]);
        } catch (Throwable $e) {
            log_message('error', (string) $e);

            return view("CodeIgniter\\Commands\\Generators\\Views\\{$this->template}", $data, ['debug' => false]);
        }
    }

    /**
     * Performs pseudo-variables contained within view file.
     */
    protected function parseTemplate(string $class, array $search = [], array $replace = [], array $data = []): string
    {
        // Retrieves the namespace part from the fully qualified class name.
        $namespace = trim(implode('\\', array_slice(explode('\\', $class), 0, -1)), '\\');
        $search[]  = '<@php';
        $search[]  = '{namespace}';
        $search[]  = '{class}';
        $replace[] = '<?php';
        $replace[] = $namespace;
        $replace[] = str_replace($namespace . '\\', '', $class);

        return str_replace($search, $replace, $this->renderTemplate($data));
    }

    /**
     * Builds the contents for class being generated, doing all
     * the replacements necessary, and alphabetically sorts the
     * imports for a given template.
     */
    protected function buildContent(string $class): string
    {
        $template = $this->prepare($class);

        if ($this->sortImports && preg_match('/(?P<imports>(?:^use [^;]+;$\n?)+)/m', $template, $match)) {
            $imports = explode("\n", trim($match['imports']));
            sort($imports);

            return str_replace(trim($match['imports']), implode("\n", $imports), $template);
        }

        return $template;
    }

    /**
     * Builds the file path from the class name.
     */
    protected function buildPath(string $class): string
    {
        $namespace = trim(str_replace('/', '\\', $this->getOption('namespace') ?? APP_NAMESPACE), '\\');

        // Check if the namespace is actually defined and we are not just typing gibberish.
        $base = Services::autoloader()->getNamespace($namespace);

        if (! $base = reset($base)) {
            CLI::error(lang('CLI.namespaceNotDefined', [$namespace]), 'light_gray', 'red');
            CLI::newLine();

            return '';
        }

        $base = realpath($base) ?: $base;
        $file = $base . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, trim(str_replace($namespace . '\\', '', $class), '\\')) . '.php';

        return implode(DIRECTORY_SEPARATOR, array_slice(explode(DIRECTORY_SEPARATOR, $file), 0, -1)) . DIRECTORY_SEPARATOR . $this->basename($file);
    }

    /**
     * Allows child generators to modify the internal `$hasClassName` flag.
     *
     * @return $this
     */
    protected function setHasClassName(bool $hasClassName)
    {
        $this->hasClassName = $hasClassName;

        return $this;
    }

    /**
     * Allows child generators to modify the internal `$sortImports` flag.
     *
     * @return $this
     */
    protected function setSortImports(bool $sortImports)
    {
        $this->sortImports = $sortImports;

        return $this;
    }

    /**
     * Allows child generators to modify the internal `$enabledSuffixing` flag.
     *
     * @return $this
     */
    protected function setEnabledSuffixing(bool $enabledSuffixing)
    {
        $this->enabledSuffixing = $enabledSuffixing;

        return $this;
    }

    /**
     * Gets a single command-line option. Returns TRUE if the option exists,
     * but doesn't have a value, and is simply acting as a flag.
     *
     * @return mixed
     */
    protected function getOption(string $name)
    {
        if (! array_key_exists($name, $this->params)) {
            return CLI::getOption($name);
        }

        return $this->params[$name] ?? true;
    }
}


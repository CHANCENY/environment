<?php

namespace Simp\Environment\Definition;

use Random\RandomException;
use Symfony\Component\Yaml\Yaml;

class Definition
{
    private array $definitions;

    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    /**
     * @throws RandomException
     * @throws DefinitionException
     */
    public function __construct(?string $storage_path = null)
    {
        $config_path = null;
        if($storage_path){
            $config_path = $storage_path;
        }
        else{
            $config_path = trim($_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'.configs', DIRECTORY_SEPARATOR);
            @mkdir($config_path,0755,true);
        }


        @mkdir($config_path.DIRECTORY_SEPARATOR.'.definition',0755,true);
        $definition_file = $config_path.DIRECTORY_SEPARATOR.'.definition'.DIRECTORY_SEPARATOR.'.definition.yml';

        if(!file_exists($definition_file)){
            $this->prepareDefinitionData($definition_file, $config_path);
        }
        $this->definitions = Yaml::parseFile($definition_file);
    }

    public static function help(): void
    {
        if (php_sapi_name() == "cli")
        {
            self::cli();
            exit(0);
        }
        echo file_get_contents(__DIR__. '/docs/help.html');
    }

    private static function cli(): void
    {
        echo "\033[1mThank you for using the Environment PHP Package to handle your configurations\033[0m

\033[4mHow to set up\033[0m

\033[32mThis package can automatically set itself up as follows:
  - The folder `.configs` will be created outside the webroot.
  - This folder will contain two subfolders: `.definition` and `.store`.
  - In `.definition`, the file `definition.yml` will be created with the following keys:
     - `hash`: Contains a hash string used to save the configurations.
     - `is_hash`: Set to `true` or `false` to specify if configurations need to be hashed before saving.
     - `enforce_backup`: Set to `true` or `false` to enforce backups.
     - `separation_mode`: Set to `true` or `false` to save configurations in separate files.
     - `max_file_size`: Specifies the maximum file size for saving configurations.
     - `store`: Contains the `.store` path.
  - Additional important files will also be created in the `.definition` folder.
  - The `.store` folder is where configurations will be saved.
\033[0m

\033[4mHow to override automatic setup\033[0m

\033[32mIf you want to manually set up your environment package requirements, follow these steps:
  - Create a `.configs/.definitions` folder outside the webroot.
  - Create a `definition.yml` file in that folder and include the following key:
     - `config_override`: Specifies the path to a `definition.yml` file that contains the necessary definitions.
\033[0m

\033[1mThank you again for installing this package.\033[0m
";
    }

    /**
     * @throws RandomException|DefinitionException
     */
    private function prepareDefinitionData(string $definition_file, string $directory): void
    {
        @touch($definition_file);
        if(file_exists($definition_file)){
            // Generate random bytes
            $randomBytes = random_bytes(ceil(50 / 2));

            // Convert to hexadecimal and trim to desired length
            $hash = substr(bin2hex($randomBytes), 0, 50);

            $definition_data = [
                'hash' => $hash,
                'is_hash' => true,
                'enforce_backup' => true,
                'separation_mode' => false,
                'max_file_size' => 1048576 * 2,
                'store' => $directory.DIRECTORY_SEPARATOR.'.store',
            ];
            file_put_contents($definition_file, Yaml::dump($definition_data));
            return;
        }
        throw new DefinitionException("Failed to generate definition file: $definition_file");
    }

    public function getStore()
    {
        return $this->definitions['store'] ?? null;
    }

    public function getHash()
    {
        return $this->definitions['hash'] ?? null;
    }

    public function getIsHash()
    {
        return $this->definitions['is_hash'] ?? false;
    }
    public function getEnforceBackup()
    {
        return $this->definitions['enforce_backup'] ?? false;
    }
    public function getSeparationMode()
    {
        return $this->definitions['separation_mode'] ?? false;
    }
    public function getMaxFileSize()
    {
        return $this->definitions['max_file_size'] ?? -1;
    }
}
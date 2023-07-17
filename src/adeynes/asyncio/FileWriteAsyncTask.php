<?php
declare(strict_types=1);

namespace adeynes\asyncio;

use pocketmine\Server;
use pocketmine\thread\NonThreadSafeValue;

class FileWriteAsyncTask extends FileOperationTask
{

    /** @var string */
    protected string $message;

    /** @var NonThreadSafeValue */
    protected NonThreadSafeValue $mode;

    public function __construct(string $file, string $message, WriteMode $mode)
    {
        $this->message = $message;
        $this->mode = new NonThreadSafeValue($mode);
        parent::__construct($file);
    }

    public function onRun(): void
    {
        $handle = fopen($this->file, $this->mode->deserialize()->getValue());

        if (!is_resource($handle)) {
            $this->setSuccess(false);
            return;
        }

        $this->setSuccess(fwrite($handle, $this->message) === false ?: true);
        fclose($handle);
    }

    protected function checkSuccess(Server $server): void
    {
        if (!$this->success) {
            $server->getLogger()->error("Unable to write to file {$this->file}");
        } else {
            $server->getLogger()->debug("Wrote to file {$this->file}");
        }
    }

}

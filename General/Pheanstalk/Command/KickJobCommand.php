<?php

namespace General\Pheanstalk\Command;

use General\Pheanstalk\Exception;
use General\Pheanstalk\Response;
use General\Pheanstalk\ResponseParser;

/**
 * The 'kick-job' command.
 * Kicks a specific buried or delayed job into a 'ready' state.
 *
 * A variant of kick that operates with a single job. If the given job
 * exists and is in a buried or delayed state, it will be moved to the
 * ready queue of the the same tube where it currently belongs.
 *
 * @author Matthieu Napoli
 * @package Pheanstalk
 * @licence http://www.opensource.org/licenses/mit-license.php
 */
class KickJobCommand
    extends AbstractCommand
    implements ResponseParser
{
    private $_job;

    /**
     * @param Job $job Pheanstalk job
     */
    public function __construct($job)
    {
        $this->_job = $job;
    }

    /* (non-phpdoc)
     * @see Command::getCommandLine()
     */
    public function getCommandLine()
    {
        return 'kick-job '.$this->_job->getId();
    }

    /* (non-phpdoc)
     * @see ResponseParser::parseRespose()
     */
    public function parseResponse($responseLine, $responseData)
    {
        if ($responseLine == Response::RESPONSE_NOT_FOUND) {
            throw new Exception\ServerException(sprintf(
                '%s: Job %d does not exist or is not in a kickable state.',
                $responseLine,
                $this->_job->getId()
            ));
        } elseif ($responseLine == Response::RESPONSE_KICKED) {
            return $this->_createResponse(Response::RESPONSE_KICKED);
        } else {
            throw new Exception('Unhandled response: '.$responseLine);
        }
    }
}

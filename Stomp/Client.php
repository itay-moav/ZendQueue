<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Queue
 */

namespace ZendQueue\Stomp;

/**
 * The Stomp client interacts with a Stomp server.
 *
 * @category   Zend
 * @package    Zend_Queue
 * @subpackage Stomp
 */
class Client
{
    /**
     * @var \ZendQueue\Stomp\Connection
     */
    protected $_connection;

    /**
     * Add a server to connections
     *
     * @param string scheme
     * @param string host
     * @param integer port
     */
    public function __construct(
        $scheme = null, $host = null, $port = null,
        $connectionClass = '\ZendQueue\Stomp\Connection',
        $frameClass = '\ZendQueue\Stomp\Frame'
    ) {
        if (($scheme !== null)
            && ($host !== null)
            && ($port !== null)
        ) {
            $this->addConnection($scheme, $host, $port, $connectionClass);
            $this->getConnection()->setFrameClass($frameClass);
        }
    }

    /**
     * Shutdown
     *
     * @return void
     */
    public function __destruct()
    {
        if ($this->getConnection()) {
            $this->getConnection()->close(true);
        }
    }

    /**
     * Add a connection to this client.
     *
     * Attempts to add this class to the client.  Returns a boolean value
     * indicating success of operation.
     *
     * You cannot add more than 1 connection to the client at this time.
     *
     * @param string  $scheme ['tcp', 'udp']
     * @param string  host
     * @param integer port
     * @param string  class - create a connection with this class; class must support \ZendQueue\Stomp\StompConnection
     * @return boolean
     */
    public function addConnection($scheme, $host, $port, $class = '\ZendQueue\Stomp\Connection')
    {
        $connection = new $class();

        if ($connection->open($scheme, $host, $port)) {
            $this->setConnection($connection);
            return true;
        }

        $connection->close();
        return false;
    }

    /**
     * Set client connection
     *
     * @param \ZendQueue\Stomp\StompConnection
     * @return void
     */
    public function setConnection(StompConnection $connection)
    {
        $this->_connection = $connection;
        return $this;
    }

    /**
     * Get client connection
     *
     * @return \ZendQueue\Stomp\Connection|null
     */
    public function getConnection()
    {
        return $this->_connection;
    }

    /**
     * Send a stomp frame
     *
     * Returns true if the frame was successfully sent.
     *
     * @param \ZendQueue\Stomp\StompFrame $frame
     * @return boolean
     */
    public function send(StompFrame $frame)
    {
        $this->getConnection()->write($frame);
        return $this;
    }

    /**
     * Receive a frame
     *
     * Returns a frame or false if none were to be read.
     *
     * @return \ZendQueue\Stomp\StompFrame|boolean
     */
    public function receive()
    {
        return $this->getConnection()->read();
    }

    /**
     * canRead()
     *
     * @return boolean
     */
    public function canRead()
    {
        return $this->getConnection()->canRead();
    }

    /**
     * creates a frame class
     *
     * @return \ZendQueue\Stomp\StompFrame
     */
    public function createFrame()
    {
        return $this->getConnection()->createFrame();
    }
}

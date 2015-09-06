<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Console\Request as ConsoleRequest;

define("REQUEST_TIMEOUT", 120000); //  msecs 2 minute

class ConsumeController extends AbstractActionController
{
    // This is the basic handler of any 0MQ messages
    // It is intended to be executed many times to provide a pool of
    // handlers

    // The invocation of this handler will be done by a crontab every minute
    // The handler itself is setup to listen for a minimum of 2 minutes
    // before allowing itself to complete. In this way at least 2 handlers
    // should be active at any point in time once the pool is primed after
    // the first 1 minute period

    public function handlerAction()
    {
        try {
            // setup the ZMQ content so as to avoid issues with conflicts
            $ctx = new \ZMQContext();

            // create a SOCKET_REP server
            $server = new \ZMQSocket($ctx, \ZMQ::SOCKET_REP);

            //  configure the server socket to not wait at close time
            // this is intended to minimise the possibility of messages being received and not handled
            // however as is mentioned in the TODO below they should be handle them explicitly

            $server->setSockOpt(\ZMQ::SOCKOPT_LINGER, 0);

            // bind it to tcp on port 5454
            $server->bind('tcp://*:5454');

            // create a Poll object to enable us to utilize the REQUEST_TIMEOUT functionality
            $poll = new \ZMQPoll();
            $poll->add($server, \ZMQ::POLL_IN);

            // initialise the read/write buffers for polling
            $read = $write = array();

            // get the time that we start the loop
            $start = time();

            do {
                // this instruction will wait for a message or the timeout to occur
                $events = $poll->poll($read, $write, REQUEST_TIMEOUT);

                // @TODO since exiting the loop will happens after this point a race condition exists
                // We need to consider solutions that will ensure ALL messages to $server are processed
                // if the loop will exit after this iteration.



                // one could check the $events variable as this contains the number of events
                // however in this situation we only want to process the $read resources and can
                // just loop through an array (if it is empty nothing will be done)
                foreach ($read as $socket) {
                    $message = $socket->recv();
                    $server->send($message . ' World');
                }

                // ensure that even when a message is processed the handler
                // does not timeout until the REQUEST_TIMEOUT period
                // has elapsed
                $active = ((time() - $start) < (REQUEST_TIMEOUT / 1000.0));
            } while ($active);
        } catch (Exception $e) {
            // handle the exception
            // @TODO
        }

        // exit the handler
        die('This handler has timed out');
    }
    /*public function indexAction()
    {
        return new ViewModel();
    }*/
}


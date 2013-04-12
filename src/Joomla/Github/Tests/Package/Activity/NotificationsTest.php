<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Github\Tests;

use Joomla\Github\Package\Activity\Notifications;
use Joomla\Registry\Registry;
use Joomla\Date\Date;

/**
 * Test class for the GitHub API package.
 *
 * @since  ¿
 */
class NotificationsTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @var    Registry  Options for the GitHub object.
	 * @since  ¿
	 */
	protected $options;

	/**
	 * @var    \PHPUnit_Framework_MockObject_MockObject  Mock client object.
	 * @since  ¿
	 */
	protected $client;

	/**
	 * @var    Notifications  Object under test.
	 * @since  ¿
	 */
	protected $object;

	/**
	 * @var    \Joomla\Http\Response  Mock response object.
	 * @since  12.3
	 */
	protected $response;

	/**
	 * @var    string  Sample JSON string.
	 * @since  12.3
	 */
	protected $sampleString = '{"a":1,"b":2,"c":3,"d":4,"e":5}';

	/**
	 * @var    string  Sample JSON error message.
	 * @since  12.3
	 */
	protected $errorString = '{"message": "Generic Error"}';

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @since   ¿
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->options = new Registry;
		$this->client = $this->getMock('\\Joomla\\Github\\Http', array('get', 'post', 'delete', 'patch', 'put'));
		$this->response = $this->getMock('\\Joomla\\Http\\Response');

		$this->object = new Notifications($this->options, $this->client);
	}

	/**
	 * Tests the  method
	 *
	 * @return  void
	 */
	public function testGetList()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/notifications?&all=1&participating=1', 0, 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getList(),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the  method
	 *
	 * @return  void
	 */
	public function testGetListRepository()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/repos/joomla/joomla-platform/notifications?&all=1&participating=1', 0, 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getListRepository('joomla', 'joomla-platform'),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the  method
	 *
	 * @return  void
	 */
	public function testMarkRead()
	{
		$this->response->code = 205;
		$this->response->body = '';

		$this->client->expects($this->once())
			->method('put')
			->with('/notifications', '{"unread":true,"read":true}', 0, 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->markRead(),
			$this->equalTo($this->response->body)
		);
	}

	/**
	 * Tests the  method
	 *
	 * @return  void
	 */
	public function testMarkReadLastRead()
	{
		$this->response->code = 205;
		$this->response->body = '';

		$date = new Date('1966-09-14');
		$data = '{"unread":true,"read":true,"last_read_at":"1966-09-14T00:00:00+00:00"}';

		$this->client->expects($this->once())
			->method('put')
			->with('/notifications', $data, 0, 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->markRead(true, true, $date),
			$this->equalTo($this->response->body)
		);
	}

	/**
	 * Tests the  method
	 *
	 * @return  void
	 */
	public function testMarkReadRepository()
	{
		$this->response->code = 205;
		$this->response->body = '';

		$data = '{"unread":true,"read":true}';

		$this->client->expects($this->once())
			->method('put')
			->with('/repos/joomla/joomla-platform/notifications', $data, 0, 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->markReadRepository('joomla', 'joomla-platform', true, true),
			$this->equalTo($this->response->body)
		);
	}

	/**
	 * Tests the  method
	 *
	 * @return  void
	 */
	public function testMarkReadRepositoryLastRead()
	{
		$this->response->code = 205;
		$this->response->body = '';

		$date = new Date('1966-09-14');
		$data = '{"unread":true,"read":true,"last_read_at":"1966-09-14T00:00:00+00:00"}';

		$this->client->expects($this->once())
			->method('put')
			->with('/repos/joomla/joomla-platform/notifications', $data, 0, 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->markReadRepository('joomla', 'joomla-platform', true, true, $date),
			$this->equalTo($this->response->body)
		);
	}

	/**
	 * Tests the  method
	 *
	 * @return  void
	 */
	public function testViewThread()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/notifications/threads/1', 0, 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->viewThread(1),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the  method
	 *
	 * @return  void
	 */
	public function testMarkReadThread()
	{
		$this->response->code = 205;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('patch')
			->with('/notifications/threads/1', '{"unread":true,"read":true}', 0, 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->markReadThread(1),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the  method
	 *
	 * @return  void
	 */
	public function testGetThreadSubscription()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('get')
			->with('/notifications/threads/1/subscription', 0, 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->getThreadSubscription(1),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the  method
	 *
	 * @return  void
	 */
	public function testSetThreadSubscription()
	{
		$this->response->code = 200;
		$this->response->body = $this->sampleString;

		$this->client->expects($this->once())
			->method('put')
			->with('/notifications/threads/1/subscription', '{"subscribed":true,"ignored":false}', 0, 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->setThreadSubscription(1, true, false),
			$this->equalTo(json_decode($this->response->body))
		);
	}

	/**
	 * Tests the  method
	 *
	 * @return  void
	 */
	public function testDeleteThreadSubscription()
	{
		$this->response->code = 204;
		$this->response->body = '';

		$this->client->expects($this->once())
			->method('delete')
			->with('/notifications/threads/1/subscription', 0, 0)
			->will($this->returnValue($this->response));

		$this->assertThat(
			$this->object->deleteThreadSubscription(1),
			$this->equalTo(json_decode($this->response->body))
		);
	}
}

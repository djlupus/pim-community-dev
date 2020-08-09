<?php

declare(strict_types=1);

namespace spec\Akeneo\Tool\Bundle\MessengerBundle\Transport\GooglePubSub;

use Akeneo\Tool\Bundle\MessengerBundle\Transport\GooglePubSub\Client;
use Akeneo\Tool\Bundle\MessengerBundle\Transport\GooglePubSub\PubSubClientFactory;
use Google\Cloud\PubSub\PubSubClient;
use Google\Cloud\PubSub\Subscription;
use Google\Cloud\PubSub\Topic;
use PhpSpec\ObjectBehavior;

/**
 * @copyright 2020 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class ClientSpec extends ObjectBehavior
{
    const PROJECT_ID = 'project-id';
    const TOPIC_NAME = 'topic-name';
    const SUBSCRIPTION_NAME = 'subscription-name';

    public function let(
        PubSubClientFactory $pubSubClientFactory,
        PubSubClient $pubSubClient,
        Topic $topic,
        Subscription $subscription
    ): void {
        $pubSubClientFactory->createPubSubClient(['projectId' => self::PROJECT_ID])
            ->willReturn($pubSubClient);
        $pubSubClient->topic(self::TOPIC_NAME)
            ->willReturn($topic);
        $topic->subscription(self::SUBSCRIPTION_NAME)
            ->willReturn($subscription);

        $this->beConstructedWith(
            self::PROJECT_ID,
            self::TOPIC_NAME,
            self::SUBSCRIPTION_NAME,
            $pubSubClientFactory
        );
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(Client::class);
    }

    public function it_is_initializable_from_a_dsn(
        PubSubClientFactory $pubSubClientFactory,
        PubSubClient $pubSubClient,
        Topic $topic,
        Subscription $subscription
    ): void {
        $this->beConstructedThrough('fromDsn', [
            'gps:',
            [
                'project_id' => self::PROJECT_ID,
                'topic_name' => self::TOPIC_NAME,
                'subscription_name' => self::SUBSCRIPTION_NAME,
                'auto_setup' => false,
            ],
            $pubSubClientFactory
        ]);
        $pubSubClientFactory->createPubSubClient(['projectId' => self::PROJECT_ID])
            ->willReturn($pubSubClient);
        $pubSubClient->topic(self::TOPIC_NAME)
            ->willReturn($topic);
        $topic->subscription(self::SUBSCRIPTION_NAME)
            ->willReturn($subscription);

        $this->shouldHaveType(Client::class);
    }

    public function it_can_be_setup(Topic $topic, Subscription $subscription): void
    {
        $topic->exists()
            ->willReturn(false);
        $subscription->exists()
            ->willReturn(false);

        $topic->create()
            ->shouldBeCalled();
        $subscription->create()
            ->shouldBeCalled();

        $this->setup();
    }

    public function it_returns_the_topic(Topic $topic): void
    {
        $this->getTopic()
            ->shouldReturn($topic);
    }

    public function it_returns_the_subscription(Subscription $subscription): void
    {
        $this->getSubscription()
            ->shouldReturn($subscription);
    }
}

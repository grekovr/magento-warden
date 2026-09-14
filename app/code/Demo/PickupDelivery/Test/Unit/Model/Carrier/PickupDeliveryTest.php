<?php

namespace Demo\PickupDelivery\Test\Unit\Model\Carrier;

use Demo\PickupDelivery\Model\Carrier\PickupDelivery;
use Demo\PickupDelivery\Model\ResourceModel\Point\Collection;
use Demo\PickupDelivery\Model\ResourceModel\Point\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;
use Magento\Shipping\Model\Rate\ResultFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class PickupDeliveryTest extends TestCase
{
    private ScopeConfigInterface|MockObject $scopeConfig;
    private ErrorFactory|MockObject $rateErrorFactory;
    private LoggerInterface|MockObject $logger;
    private ResultFactory|MockObject $rateResultFactory;
    private MethodFactory|MockObject $rateMethodFactory;
    private CollectionFactory|MockObject $collectionFactory;

    protected function setUp(): void
    {
        $this->scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $this->rateErrorFactory = $this->createMock(ErrorFactory::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->rateResultFactory = $this->createMock(ResultFactory::class);
        $this->rateMethodFactory = $this->createMock(MethodFactory::class);
        $this->collectionFactory = $this->createMock(CollectionFactory::class);
    }

    public function testCollectRatesReturnsFalseWhenCarrierIsDisabled(): void
    {
        $this->scopeConfig
            ->expects($this->once())
            ->method('isSetFlag')
            ->with('carriers/pickupdelivery/active', 'store', null)
            ->willReturn(false);

        $this->collectionFactory->expects($this->never())->method('create');

        $carrier = $this->createCarrier();

        $this->assertFalse($carrier->collectRates($this->createMock(RateRequest::class)));
    }

    public function testCollectRatesReturnsFalseWhenNoActivePickupPointsExist(): void
    {
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with('is_active', 1)
            ->willReturnSelf();
        $collection->expects($this->once())->method('getSize')->willReturn(0);

        $this->scopeConfig->method('isSetFlag')->willReturn(true);
        $this->collectionFactory->expects($this->once())->method('create')->willReturn($collection);
        $this->rateResultFactory->expects($this->never())->method('create');

        $carrier = $this->createCarrier();

        $this->assertFalse($carrier->collectRates($this->createMock(RateRequest::class)));
    }

    public function testCollectRatesReturnsRateWhenActivePickupPointExists(): void
    {
        $collection = $this->createMock(Collection::class);
        $collection->expects($this->once())
            ->method('addFieldToFilter')
            ->with('is_active', 1)
            ->willReturnSelf();
        $collection->expects($this->once())->method('getSize')->willReturn(1);

        $result = $this->createMock(Result::class);
        $priceCurrency = $this->createMock(\Magento\Framework\Pricing\PriceCurrencyInterface::class);
        $priceCurrency->expects($this->once())->method('round')->with(80.0)->willReturn(80.0);
        $method = new Method($priceCurrency);

        $result->expects($this->once())->method('append')->with($method);

        $this->scopeConfig->method('isSetFlag')->willReturn(true);
        $this->scopeConfig->method('getValue')->willReturnMap([
            ['carriers/pickupdelivery/price', 'store', null, '80'],
            ['carriers/pickupdelivery/title', 'store', null, 'Pickup Delivery'],
            ['carriers/pickupdelivery/name', 'store', null, 'Pickup Delivery'],
        ]);
        $this->collectionFactory->method('create')->willReturn($collection);
        $this->rateResultFactory->expects($this->once())->method('create')->willReturn($result);
        $this->rateMethodFactory->expects($this->once())->method('create')->willReturn($method);

        $carrier = $this->createCarrier();

        $rateResult = $carrier->collectRates($this->createMock(RateRequest::class));

        $this->assertSame($result, $rateResult);
        $this->assertSame('pickupdelivery', $method->getData('carrier'));
        $this->assertSame('Pickup Delivery', $method->getData('carrier_title'));
        $this->assertSame('pickupdelivery', $method->getData('method'));
        $this->assertSame('Pickup Delivery', $method->getData('method_title'));
        $this->assertSame(80.0, $method->getData('price'));
        $this->assertSame(80.0, $method->getData('cost'));
    }

    private function createCarrier(): PickupDelivery
    {
        return new PickupDelivery(
            $this->scopeConfig,
            $this->rateErrorFactory,
            $this->logger,
            $this->rateResultFactory,
            $this->rateMethodFactory,
            $this->collectionFactory
        );
    }
}

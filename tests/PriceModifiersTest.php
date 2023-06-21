<?php

namespace App\Tests;

use App\DTO\LowestPriceEnquiry;
use App\Entity\Product;
use App\Entity\Promotion;
use App\Filter\Modifier\DateRangeMultiplier;
use App\Filter\Modifier\EvenItemsMultiplier;
use App\Filter\Modifier\FixedPriceVoucher;

class PriceModifiersTest extends ServiceTestCase
{
    /** @test */
    public function date_range_multiplier_returns_correctly_modified_price(): void
    {
        // Given
        $product = new Product();
        $product->setPrice(100);

        $promotion = new Promotion();
        $promotion->setName('Black Friday half price sale');
        $promotion->setAdjustment(0.5);
        $promotion->setCriteria(["from" => "2022-11-25", "to" => "2022-11-28"]);
        $promotion->setType('date_range_multiplier');

        $enquiry = new LowestPriceEnquiry();
        $enquiry->setProduct($product);
        $enquiry->setRequestDate('2022-11-27');
        $enquiry->setQuantity(5);

        $dateRangeModifier = new DateRangeMultiplier();

        // When
        $modifiedPrice = $dateRangeModifier->modify(100, 5, $promotion, $enquiry);

        // Then
        $this->assertEquals(250, $modifiedPrice);
    }

    /** @test */
    public function fixed_price_voucher_returns_correctly_modified_price(): void
    {
        // Given
        $product = new Product();
        $product->setPrice(100);

        $promotion = new Promotion();
        $promotion->setName('Voucher OU812');
        $promotion->setAdjustment(100);
        $promotion->setCriteria(["code" => "OU812"]);
        $promotion->setType('fixed_price_voucher');

        $enquiry = new LowestPriceEnquiry();
        $enquiry->setQuantity(5);
        $enquiry->setVoucherCode("OU812");

        $fixedPriceVoucher = new FixedPriceVoucher();

        // When
        $modifiedPrice = $fixedPriceVoucher->modify(150, 5, $promotion, $enquiry);

        // Then
        $this->assertEquals(500, $modifiedPrice);
    }

    /** @test */
    public function EvenItemsMultiplier_returns_a_correctly_modified_price(): void
    {
        // Given
        $enquiry = new LowestPriceEnquiry();
        $enquiry->setQuantity(5);

        $promotion = new Promotion();
        $promotion->setName('Buy one get one free');
        $promotion->setAdjustment(0.5);
        $promotion->setCriteria(["minimum_quantity" => 2]);
        $promotion->setType('even_items_multiplier');

        $evenItemsMultiplier = new EvenItemsMultiplier();

        // When
        $modifiedPrice = $evenItemsMultiplier->modify(100, 5, $promotion, $enquiry);

        // Then
        // ((100 * 4) * 0.5) + (1 * 100)
        $this->assertEquals(300, $modifiedPrice);
    }

    /** @test */
    public function EvenItemsMultiplier_correctly_calculates_alternatives(): void
    {
        // Given
        $enquiry = new LowestPriceEnquiry();
        $enquiry->setQuantity(5);

        $promotion = new Promotion();
        $promotion->setName('Buy one get one half price');
        $promotion->setAdjustment(0.75);
        $promotion->setCriteria(["minimum_quantity" => 2]);
        $promotion->setType('even_items_multiplier');

        $evenItemsMultiplier = new EvenItemsMultiplier();

        // When
        $modifiedPrice = $evenItemsMultiplier->modify(100, 5, $promotion, $enquiry);

        // Then
        // 300 + 100
        $this->assertEquals(400, $modifiedPrice);
    }
}
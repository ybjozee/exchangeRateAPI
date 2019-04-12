<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExchangeRateRepository")
 */
class ExchangeRate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string")
     */
    private $currency;

    /**
     * @ORM\Column(type="string")
     */
    private $year;

    /**
     * @ORM\Column(type="string")
     */
    private $month;

    /**
     * @ORM\Column(type="string")
     */
    private $buyingRate;

    /**
     * @ORM\Column(type="string")
     */
    private $centralRate;

    /**
     * @ORM\Column(type="string")
     */
    private $sellingRate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(string $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getMonth(): ?string
    {
        return $this->month;
    }

    public function setMonth(string $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getBuyingRate(): ?string
    {
        return $this->buyingRate;
    }

    public function setBuyingRate(string $buyingRate): self
    {
        $this->buyingRate = $buyingRate;

        return $this;
    }

    public function getCentralRate(): ?string
    {
        return $this->centralRate;
    }

    public function setCentralRate(string $centralRate): self
    {
        $this->centralRate = $centralRate;

        return $this;
    }

    public function getSellingRate(): ?string
    {
        return $this->sellingRate;
    }

    public function setSellingRate(string $sellingRate): self
    {
        $this->sellingRate = $sellingRate;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'date' => $this->date->format('n/j/Y'),
            'buyingRate' => $this->buyingRate,
            'sellingRate' => $this->sellingRate,
            'centralRate' => $this->centralRate
        ];
    }
}
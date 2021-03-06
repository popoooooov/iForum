<?php

/*
 * This file is part of the "php-paradise/array-keys-converter" package.
 * (c) Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use App\Traits\IsAuthorTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MessagesRepository")
 */
class Messages
{
    use IsAuthorTrait;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Topics", inversedBy="messages")
     * @ORM\JoinColumn(nullable=true)
     */
    private $topics;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="text")
     */
    private $text;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Users", inversedBy="messages")
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;

    /**
     * Messages constructor.
     *
     * @param $date
     */
    public function __construct()
    {
        $this->date = new \DateTime();
    }


    public function getId(): int
    {
        return $this->id;
    }

    public function getTopics(): Topics
    {
        return $this->topics;
    }

    public function setTopics(Topics $topics)
    {
        $this->topics = $topics;
    }

    public function getDate(): ?string
    {
        return $this->date->format('Y-m-d H:i:s');
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getAuthor(): Users
    {
        return $this->author;
    }

    public function setAuthor(Users $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function isAuthorOfMessage(int $userId): bool
    {
        return $this->getAuthor()->getId() === $userId;
    }
}

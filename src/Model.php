<?php /** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

namespace DataHead\InterfazFramework;
use Doctrine\ORM\Mapping as ORM;

class Model
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}

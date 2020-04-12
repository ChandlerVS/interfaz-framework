<?php /** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

namespace DataHead\InterfazFramework;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;

class Model
{
    /** @var EntityManager $entityManager */
    protected $entityManager;
    public function __construct()
    {
        $this->entityManager = Framework::getInstance()->container->get(EntityManager::class);
    }

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

    public function save() {
        $this->entityManager->persist($this);
        $this->entityManager->flush();
    }
}

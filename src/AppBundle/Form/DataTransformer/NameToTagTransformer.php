<?php
namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Tag;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class NameToTagTransformer implements DataTransformerInterface
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param  Tag|null $tags
     * @return string
     */
    public function transform($tags)
    {
        if (!is_array($tags)) return "";

        $tagString = "";
        /** @var Tag $tag */
        foreach($tags as $tag) {
            if ($tagString != "") $tagString .= ", ";
            $tagString .= $tag->getName();
        }

        return $tagString;
    }

    /**
     * @param  string $tagName
     * @return Tag|null
     * @throws TransformationFailedException if object (tag) is not found.
     */
    public function reverseTransform($tagName)
    {
        if (!$tagName) {
            return;
        }

        $tags = explode(",", $tagName);
        $tagsReturn = array();
        foreach($tags as $tagNameUnique) {
            $tagNameUnique = trim($tagNameUnique);
            $tag = $this->entityManager
                ->getRepository('AppBundle:Tag')
                ->findOneByName($tagNameUnique)
            ;

            if (null === $tag) {
                $tag = new Tag();
                $tag->setName($tagNameUnique);
                $this->entityManager->persist($tag);
                $this->entityManager->flush();
            }

            $tagsReturn[] = $tag;
        }


        return $tagsReturn;
    }
}
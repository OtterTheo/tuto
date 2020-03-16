<?php


namespace App\Controller;


use App\Entity\Property;
use App\Repository\PropertyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PropertyController extends AbstractController
{
    /**
     * @var PropertyRepository
     */

    private $repository;
    /**
     * @var ObjectManager
     */
    private $em;


    public function __construct(PropertyRepository $repository, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/biens" , name="property.index")
     * @return Response
     */
    public function index():Response
    {
//        $property = new Property();
//        $property->setTitle('Mon premier bien')
//                 ->setPrice(200000)
//                 ->setRooms(4)
//                 ->setBedrooms(3)
//                 ->setDescription('Une petite description')
//                 ->setSurface(60)
//                 ->setFloor(4)
//                 ->setHea(1)
//                 ->setCity('Montepellier')
//                 ->setAddress('15 Boulevard Gambetta')
//                 ->setPostalCode('34000');
//        $em = $this->getDoctrine()->getManager();
//        $em->persist($property);
//        $em->flush();
//        $property = $this->repository->findAll();
//        $property = $this->repository->find(1);
//        $property = $this->repository->findOneBy(['floor' => 4]);
//        $property[0]->setSold(true);
//        $this->em->flush();

        return $this->render('property/index.html.twig', [
            'current_menu' => 'properties'
        ]);
    }

    /**
     * @Route("/biens/{slug}-{id}" , name="property.show", requirements={"slug": "[a-z0-9\-]*"})
     * @param Property $property
     * @return Response
     */
    
    public function show(Property $property, string $slug): Response{

//        $property = $this->repository->find($id);
        if($property->getSlug() !== $slug){
           return $this->redirectToRoute('property.show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug()
,            ], 301);
        }
        return $this->render('property/show.html.twig', [
                'property' => $property,
                'current_menu' => 'properties'
            ]);
    }
}
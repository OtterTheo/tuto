<?php


namespace App\Controller;


use App\Entity\Contact;
use App\Entity\Property;
use App\Entity\PropertySearch;
use App\Form\ContactType;
use App\Form\PropertySearchType;
use App\Notification\ContactNotification;
use App\Repository\PropertyRepository;
use Doctrine\Persistence\ObjectManager;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, Request $request):Response
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

        $search = new PropertySearch();
        $form = $this->createForm(PropertySearchType::class, $search);
        $form->handleRequest($request);

        $properties = $paginator->paginate(
            $this->repository->findAllVisibleQuery($search),
            $request->query->getInt('page', 1), 12
        );
        return $this->render('property/index.html.twig', [
            'current_menu' => 'properties',
            'properties' => $properties,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/biens/{slug}-{id}" , name="property.show", requirements={"slug": "[a-z0-9\-]*"})
     * @param Property $property
     * @param string $slug
     * @param Request $request
     * @param ContactNotification $notification
     * @return Response
     */
//    On passe en paramètre le titre du bien slugifier et le bien en intégralité
    public function show(Property $property, string $slug, Request $request, ContactNotification $notification): Response{

        if($property->getSlug() !== $slug){
           return $this->redirectToRoute('property.show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug()
,            ], 301);
        }
        $contact = new Contact();
        $contact->setProperty($property);
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $notification->notify($contact);
            $this->addFlash('success', 'Votre email a bien été envoyé !');

            return $this->redirectToRoute('property.show', [
                'id' => $property->getId(),
                'slug' => $property->getSlug()
                ]);
        }

        return $this->render('property/show.html.twig', [
                'property' => $property,
                'current_menu' => 'properties',
                'form' => $form->createView()
            ]);
    }
}
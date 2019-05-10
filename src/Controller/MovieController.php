<?php
namespace App\Controller;
use mysql_xdevapi\Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use App\Entity\Movie;
use App\Form\MovieType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Movie controller.
 * @Route("/api", name="api_")
 */
class MovieController extends FOSRestController
{
    private $repository;


    /**
     * Lists all Movies.
     * @Rest\Get("/movies")
     *
     * @return Response
     */
    public function getMovieAction()
    {
        $this->repository = $this->getDoctrine()->getRepository(Movie::class);
        $movies = $this->repository->findall();
        return $this->handleView($this->view($movies));
    }
    /**
     * Create Movie.
     * @Rest\Post("/movie")
     *
     * @return Response
     */
    public function postMovieAction(Request $request)
    {
//        dump($request); exit;
        $movie = new Movie();
        $form = $this->createForm(MovieType::class, $movie);
        $data = json_decode($request->getContent(), true);
        $form->setData($data);
        if ($form->submit() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($movie);
            $em->flush();
            return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
        }
        return $this->handleView($this->view($form->getErrors()));
    }

    /**
     * Get Single Movie
     * @Rest\Get("/movies/{id}")
     *
     * @return mixed
     */
    public function GetSingleMovieAction($id){
//        return $id;
//        $movie = new Movie();
        $this->repository = $this->getDoctrine()->getRepository(Movie::class);
        $response = null;
        try{
            $response = $this->repository->find($id);
//            return $response;
            return $this->handleView($this->view($response));
        }catch (\Exception $ex){
            new NotFoundHttpException("Record is  Not Available");
        }

    }

    /**
     * Update Single Movie
     * @Rest\Put("/movie/{id}")
     *
     * return mixed
     */
    public function UpdateSingleRecord(Request $request, $id){
        $this->repository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $this->repository->find($id);
        try{
            $form = $this->createForm(MovieType::class, $movie);
            $data = json_decode($request->getContent(), true);
            $form->submit($data);
            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($movie);
                $em->flush();
                return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
            }
        }catch (\Exception $ex){
            return $this->handleView($this->view(['status' => $ex->getMessage()], $ex->getCode()));
        }
    }

    /**
     * Delete Single Movie
     * @Rest\Delete("/movie/{id}")
     *
     * return mixed
     */
    public function DeleteSingleRecord($id){
        $this->repository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $this->repository->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($movie);
        $em->flush();
        return $this->handleView($this->view(['status' => 'ok'], Response::HTTP_CREATED));
    }
}
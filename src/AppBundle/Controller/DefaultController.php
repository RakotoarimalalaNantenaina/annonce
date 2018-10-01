<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\Annonce;
use AppBundle\Entity\Media;
use AppBundle\Form\AnnonceType;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]);
    }
     /**
     * @Route("/acceuil", name="acceuil")
     */
    public function acceuilAction(){

        return $this->render("acceuil.html.twig");
    }
        /**
     * @Route("/insertion", name="inserer")
     */
    public function testAction(){
        return this;
    }
     /**
     * @Route("/insertion", name="inserer")
     */

     public function AjouterAction(Request $req){
         $titre = $req->request->get("titre");
         $decrire = $req->request->get("decrire");
         $creer = $req->request->get("create");
         $modifier = $req->request->get("update");
         $fichier = $req->request->get("fichier");
         $data = $req->request->get("data");
         $tableau = (string)$data;
         
         


        $entityManager = $this->getDoctrine()->getManager();
        
        $annonce = new Annonce();

        $annonce->setTitre($titre);
        $annonce->setDescription($decrire);
        $annonce->setCreatedDate($creer);
        $annonce->setUpdatedDate($modifier);

        

        $tab = explode('data:', $tableau);
        unset($tab[0]);
        function generateRandomString($length) {
                    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                    $charactersLength = strlen($characters);
                    $randomString = '';
                    for ($i = 0; $i < $length; $i++) {
                        $randomString .= $characters[rand(0, $charactersLength - 1)];
                    }
                    return $randomString;
                }
         foreach($tab as $key => $tabl){
            $media = new Media();
            $filetyp = explode(';', $tabl)[0];
            $filetype = explode('/', $filetyp)[1];
            $data = explode(',', $tabl)[1];
            $filename = generateRandomString(20);
            $url = '/'.$filename .'.'.$filetype;
            ini_get("allow_url_fopen");
            file_put_contents( $this->getParameter('media_directory') . $url , base64_decode($data));

            $media->setNom($filename);
            $media->setUrl($url);
            $media->setType($filetype);
            $media->setAnnonce($annonce);

            $entityManager->persist($media);
         }

        $entityManager->persist($annonce);

        $entityManager->flush();

        return  $this->RedirectToroute('liste');
     }
      /**
     * @Route("/liste_annonce", name="liste")
     */
     public function ListeAction(){
        $em = $this->getDoctrine()->getRepository(Annonce::class);
        $annonce = $em->findAll();

        return $this->render("liste.html.twig",["annonce"=>$annonce]);
     }
     /**
     * @Route("/supprimer/{id}", name="supprimer")
     */
    public function supprimerAction($id){
        $entityManager = $this->getDoctrine()->getManager();
        $find = $entityManager->getRepository(Annonce::class)->find($id);

        $entityManager->remove($find);
        $entityManager->flush();

         return  $this->RedirectToroute('liste');
    }
    /**
     * @Route("/modifier_annonce/{id}", name="modifier")
     */
     public function ModifierAction(Request $rep,$id){
        $em = $this->getDoctrine()->getManager();
        $findid = $em->getRepository(Annonce::class)->find($id);

         $form = $this->createForm(AnnonceType::class, $findid);

         $form ->handleRequest($rep);
                if($form->isSubmitted()){
                    $em->flush();
                    return $this->redirectToRoute('liste');
                }
        return $this->render('modifier.html.twig',[
        'formAnnonce'=>$form->createView()
        ]
        );    

     }
}

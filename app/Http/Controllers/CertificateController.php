<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateController extends Controller
{
    public function index(){
        return view('certificate');
    }

    public function generateCertificate($download = false)
    {
        $name = "Afiifatuts Tsaaniyah Abdullah";
        $credential ="1234ABC";

        //Generate QR Code 
        $qrCode = QrCode::format('png')->size(500)->generate($credential);
        $qrCodePath = public_path('qr/'.$credential.'.png');
        file_put_contents($qrCodePath,$qrCode);


        //Create instance PDF
        $pdf = new Fpdi();

        $pathToTemplate = public_path('certicate/Certificate.pdf');
        $pdf->setSourceFile($pathToTemplate);
        $template = $pdf->importPage(1);

        $size = $pdf->getTemplateSize($template);

        $pdf->AddPage($size['orientation'],[$size['width'],$size['height']]);
        $pdf->useTemplate($template,0,0,$size['width'],$size['height']);

        $pdf->SetFont('Helvetica');
        $pdf->SetFontSize(30);
        $pdf->SetXY(35,120);
        $pdf->Write(0,$name);

        $pdf->SetFont('Helvetica');
        $pdf->SetFontSize(15);
        $pdf->SetXY(238, 177);
        $pdf->Write(0,$credential);

        $pdf->Image($qrCodePath,217,133,40,40);

        $fileName = 'Certificate '.$name.'.pdf';


        if($download){
            return response()->make($pdf->Output('D',$fileName),200,[
                'Content-Type'=>'application/pdf',
                'Content-Disposition'=>'attachment; fileName="'.$fileName.'"'
            ]);
        }else{
            return response()->make($pdf->Output('I',$fileName),200,[
                'Content-Type'=>'application/pdf',
                'Content-Disposition'=>'inline; fileName="'.$fileName.'"'
            ]);
        }
    }

    public function viewCertificate()
    {
        return $this->generateCertificate(false);
    }

    public function downloadCertificate()
    {
        return $this->generateCertificate(true);
    }
}

<?php

namespace App\Service\TPM;

use App\Model\TPM\MachineFittingModel;
use App\Model\TPM\TemplateModel;
use App\Service\Service;

class MachineTemplateService extends Service
{

  public function getMachineTemplates()
  {
    return TemplateModel::all();
  }


  public function getTemplateFittings($template_id)
  {
    return MachineFittingModel::query()->where('template_id', '=', $template_id)->get();
  }
}

<?php


namespace Ast\MwbExporterExtra\Formatter\Doctrine2\AnnotationSf4\Model;

use Ast\MwbExporterExtra\Formatter\Doctrine2\AnnotationSf3\Formatter;

use MwbExporter\Formatter\Doctrine2\Annotation\Model\Column as BaseColumn;
use MwbExporter\Writer\WriterInterface;

class Column extends BaseColumn
{
    public function writeVar(WriterInterface $writer)
    {
        if (!$this->isIgnored()) {
            $comment = $this->getComment();
            $writer
                ->write('/**')
                ->writeIf($comment, $comment)
                ->writeIf($this->isPrimary,
                    ' * '.$this->getTable()->getAnnotation('Id'))
                ->write(' * '.$this->getTable()->getAnnotation('Column', $this->asAnnotation()))
                ->writeIf($this->isAutoIncrement(),
                    ' * '.$this->getTable()->getAnnotation('GeneratedValue', array('strategy' => strtoupper($this->getConfig()->get(Formatter::CFG_GENERATED_VALUE_STRATEGY)))))
                ->write(' */');
            $type= $this->getFormatter()->getDatatypeConverter()->getMappedType($this);
            if(!$this->isPrimary){
                $default=$this->parameters->get('defaultValue');
                if( $type=="string" &&  $default!="" && $default!=null && $default!='NULL'){

                    $writer->write('protected $'.$this->getColumnName()."='".$default."';");
                }elseif( ($type=="integer" || $type=="float") &&  $default!="" && $default!=null && $default!='NULL'){

                    $writer->write('protected $'.$this->getColumnName().'= '.($default*1).';');
                }elseif($type=="boolean" && $default!="" && $default!=null && $default!='NULL'){

                    $writer->write('protected $'.$this->getColumnName().'= '.($default=='TRUE'? "true":($default=='FALSE'? "false":(($default*1)==1?"true":"false"))).';');
                }else{
                    $writer->write('protected $'.$this->getColumnName().';');
                }
            }else{
                $writer->write('protected $'.$this->getColumnName().';');
            }
            $writer->write('');
            ;
        }

        return $this;
    }

    public function asAnnotation()
    {
        $type= $this->getFormatter()->getDatatypeConverter()->getMappedType($this);
        $attributes = array(
            'name' => ($columnName = $this->getTable()->quoteIdentifier($this->getColumnName())) !== $this->getColumnName() ? $columnName : null,
            'type' => $type,
        );
        if (($length = $this->parameters->get('length')) && ($length != -1)) {
            $attributes['length'] = (int) $length;
        }
        if (($precision = $this->parameters->get('precision')) && ($precision != -1) && ($scale = $this->parameters->get('scale')) && ($scale != -1)) {
            $attributes['precision'] = (int) $precision;
            $attributes['scale'] = (int) $scale;
        }
        if ($this->isNullableRequired()) {
            $attributes['nullable'] = $this->getNullableValue();
        }
        if($this->isUnsigned()) {
            $attributes['options'] = array('unsigned' => true);
        }
        if(!$this->isPrimary){
            $default=$this->parameters->get('defaultValue');
            if( ($type=="integer" || $type=="float") &&  $default!="" && $default!=null && $default!='NULL' ){
                $opciones=(isset($attributes['options']))?$attributes['options']:array();
                $opciones["default"]=($default*1);
                $attributes['options']=$opciones;
            }
            if($type=="boolean" && $default!="" && $default!=null && $default!='NULL'){
                $opciones=(isset($attributes['options']))?$attributes['options']:array();
                $opciones["default"]=($default=='TRUE'? 1:($default=='FALSE'? 0: ($default*1)));
                $attributes['options']=$opciones;
            }
            if($type=="string" && $default!="" && $default!=null && $default!='NULL'){
                $opciones=(isset($attributes['options']))?$attributes['options']:array();
                $opciones["default"]=$default;
                $attributes['options']=$opciones;
            }

        }


        return $attributes;
    }


}

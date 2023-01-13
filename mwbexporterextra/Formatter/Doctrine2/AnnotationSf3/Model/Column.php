<?php

/*
 * The MIT License
 *
 * Copyright (c) 2010 Johannes Mueller <circus2(at)web.de>
 * Copyright (c) 2012-2014 Toha <tohenk@yahoo.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace Ast\MwbExporterExtra\Formatter\Doctrine2\AnnotationSf3\Model;

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

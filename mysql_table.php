<?php
require('libs/fpdf/fpdf.php');

class PDF_MySQL_Table extends FPDF
{
	var $ProcessingTable=false;
	var $aCols=array();
	var $TableX;
	var $HeaderColor;
	var $RowColors;
	var $ColorIndex;

	function Header()
	{

		//Print the table header if necessary
		if($this->ProcessingTable)
			$this->TableHeader();
	}

	function TableHeader()
	{
		$this->SetFont('Arial','B',12);
		$this->SetX($this->TableX);
		$fill=!empty($this->HeaderColor);
		if($fill)
			$this->SetFillColor($this->HeaderColor[0],$this->HeaderColor[1],$this->HeaderColor[2]);
		foreach($this->aCols as $col)
			$this->Cell($col['w'],6,$col['c'],1,0,'C',$fill);
		$this->Ln();
	}

	function Row($data)
	{
		$this->SetX($this->TableX);
		$ci=$this->ColorIndex;
		$fill=!empty($this->RowColors[$ci]);
		if($fill)
			$this->SetFillColor($this->RowColors[$ci][0],$this->RowColors[$ci][1],$this->RowColors[$ci][2]);
		foreach($this->aCols as $col)
			///$this->Cell($col['w'],5,$data[$col['f']],1,0,$col['a'],$fill);
			$this->CellFitScale($col['w'],5,$data[$col['f']],1,0,$col['a'],$fill);
		$this->Ln();
		$this->ColorIndex=1-$ci;
	}

	function CalcWidths($width,$align)
	{
		//Compute the widths of the columns
		$TableWidth=0;
		foreach($this->aCols as $i=>$col)
		{
			$w=$col['w'];
			if($w==-1)
				$w=$width/count($this->aCols);
			elseif(substr($w,-1)=='%')
				$w=$w/100*$width;
			$this->aCols[$i]['w']=$w;
			$TableWidth+=$w;
		}
		//Compute the abscissa of the table
		if($align=='C')
			$this->TableX=max(($this->w-$TableWidth)/2,0);
		elseif($align=='R')
			$this->TableX=max($this->w-$this->rMargin-$TableWidth,0);
		else
			$this->TableX=$this->lMargin;
	}

	function AddCol($field=-1,$width=-1,$caption='',$align='C')
	{
		//Add a column to the table
		if($field==-1)
			$field=count($this->aCols);
		$this->aCols[]=array('f'=>$field,'c'=>$caption,'w'=>$width,'a'=>$align);
	}

	function Table($query,$prop=array())
	{
		//Issue query
		$res=mysql_query($query) or die('Error: '.mysql_error()."<BR>Query: $query");
		//Add all columns if none was specified
		if(count($this->aCols)==0)
		{
			$nb=mysql_num_fields($res);
			for($i=0;$i<$nb;$i++)
				$this->AddCol();
		}
		//Retrieve column names when not specified
		foreach($this->aCols as $i=>$col)
		{
			if($col['c']=='')
			{
				if(is_string($col['f']))
					$this->aCols[$i]['c']=ucfirst($col['f']);
				else
					$this->aCols[$i]['c']=ucfirst(mysql_field_name($res,$col['f']));
			}
		}
		//Handle properties
		if(!isset($prop['width']))
			$prop['width']=0;
		if($prop['width']==0)
			$prop['width']=$this->w-$this->lMargin-$this->rMargin;
		if(!isset($prop['align']))
			$prop['align']='C';
		if(!isset($prop['padding']))
			$prop['padding']=$this->cMargin;
		$cMargin=$this->cMargin;
		$this->cMargin=$prop['padding'];
		if(!isset($prop['HeaderColor']))
			$prop['HeaderColor']=array();
		$this->HeaderColor=$prop['HeaderColor'];
		if(!isset($prop['color1']))
			$prop['color1']=array();
		if(!isset($prop['color2']))
			$prop['color2']=array();
		$this->RowColors=array($prop['color1'],$prop['color2']);
		//Compute column widths
		$this->CalcWidths($prop['width'],$prop['align']);
		//Print header
		$this->TableHeader();
		//Print rows
		$this->SetFont('Arial','',11);
		$this->ColorIndex=0;
		$this->ProcessingTable=true;
		while($row=mysql_fetch_array($res))
			$this->Row($row);
		$this->ProcessingTable=false;
		$this->cMargin=$cMargin;
		$this->aCols=array();
	}
	//Cell with horizontal scaling if text is too wide
	function CellFit($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $scale=false, $force=true)
	{
		//Get string width
		$str_width=$this->GetStringWidth($txt);

		//Calculate ratio to fit cell
		if($w==0)
			$w = $this->w-$this->rMargin-$this->x;
		$ratio = ($w-$this->cMargin*2)/$str_width;

		$fit = ($ratio < 1 || ($ratio > 1 && $force));
		if ($fit)
		{
			if ($scale)
			{
				//Calculate horizontal scaling
				$horiz_scale=$ratio*100.0;
				//Set horizontal scaling
				$this->_out(sprintf('BT %.2F Tz ET',$horiz_scale));
			}
			else
			{
				//Calculate character spacing in points
				$char_space=($w-$this->cMargin*2-$str_width)/max($this->MBGetStringLength($txt)-1,1)*$this->k;
				//Set character spacing
				$this->_out(sprintf('BT %.2F Tc ET',$char_space));
			}
			//Override user alignment (since text will fill up cell)
			$align='';
		}

		//Pass on to Cell method
		$this->Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);

		//Reset character spacing/horizontal scaling
		if ($fit)
			$this->_out('BT '.($scale ? '100 Tz' : '0 Tc').' ET');
	}

	//Cell with horizontal scaling only if necessary
	function CellFitScale($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
	{
		$this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,true,false);
	}

	//Cell with horizontal scaling always
	function CellFitScaleForce($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
	{
		$this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,true,true);
	}

	//Cell with character spacing only if necessary
	function CellFitSpace($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
	{
		$this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,false,false);
	}

	//Cell with character spacing always
	function CellFitSpaceForce($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
	{
		//Same as calling CellFit directly
		$this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,false,true);
	}

	//Patch to also work with CJK double-byte text
	function MBGetStringLength($s)
	{
		if($this->CurrentFont['type']=='Type0')
		{
			$len = 0;
			$nbbytes = strlen($s);
			for ($i = 0; $i < $nbbytes; $i++)
			{
				if (ord($s[$i])<128)
					$len++;
				else
				{
					$len++;
					$i++;
				}
			}
			return $len;
		}
		else
			return strlen($s);
	}
	function FancyTable($header, $data)
	{
		// Colors, line width and bold font
		$this->SetFillColor(255,0,0);
		$this->SetTextColor(255);
		$this->SetDrawColor(128,0,0);
		$this->SetLineWidth(.3);
		$this->SetFont('','B');
		// Header
		$w = array(40, 35, 40, 45);
		for($i=0;$i<count($header);$i++)
			$this->Cell($w[$i],7,$header[$i],1,0,'C',true);
		$this->Ln();
		// Color and font restoration
		$this->SetFillColor(224,235,255);
		$this->SetTextColor(0);
		$this->SetFont('');
		// Data
		$fill = false;
		foreach($data as $row)
		{
			$this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
			$this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
			$this->Cell($w[2],6,number_format($row[2]),'LR',0,'R',$fill);
			$this->Cell($w[3],6,number_format($row[3]),'LR',0,'R',$fill);
			$this->Ln();
			$fill = !$fill;
		}
		// Closing line
		$this->Cell(array_sum($w),0,'','T');
	}
}
?>

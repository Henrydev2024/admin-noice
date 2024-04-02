<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class ReportDetailExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $list_data;

    public function __construct($list_data)
    {
        $this->list_data = $list_data;
    }

    public function collection()
    {
        $data = collect();
        foreach ($this->list_data as $val) {
            $resultText = ($val->status == 0) ? 'Thắng' : 'Thua';
            $data->push([
                'Ref Code:'.$val->transfer_code. ', Thời gian: '.$val->transaction_time,
                $val->game_provider_text,
                $val->amount,
                $val->win_loss - $val->amount,
                $resultText,
                $val->commission_refund,
                $val->balance_before,
                $val->balance_after,
            ]);
        }
        return $data;
    }
    public function headings(): array{
        return ['Thời gian','Trò chơi','Tiền cược','Thắng thua','Trạng thái','Hoàn trả','Số dư trước','Số dư sau'];
    }
}

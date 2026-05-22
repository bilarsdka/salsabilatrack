import sys
import pickle
import numpy as np
import pandas as pd
import os
import json

def konversi_jam(waktu_str):
    try:
        h, m = map(int, waktu_str.split(':'))
        return round(h + m / 60, 2)
    except:
        return float(waktu_str)

def get_jam_kategori(jam_float):
    base_dir = os.path.dirname(os.path.abspath(__file__))
    cluster_path = os.path.join(base_dir, 'cluster_model.pkl')
    
    if not os.path.exists(cluster_path):
        return 'normal', 45
    
    with open(cluster_path, 'rb') as f:
        cluster_data = pickle.load(f)
    
    jam_int = int(jam_float)
    jam_map = cluster_data['jam_map']
    
    if jam_int in jam_map:
        info = jam_map[jam_int]
        return info['kategori'], info['avg_estimasi']
    
    return 'normal', 45


def predict(jam_str, jumlah_antrian, estimasi_menit, order_items_str='', mode='full'):
    base_dir = os.path.dirname(os.path.abspath(__file__))
    model_path = os.path.join(base_dir, 'model.pkl')
    
    jam = konversi_jam(jam_str)
    
    # [1] Klasifikasi: Telat/Tepat
    with open(model_path, 'rb') as f:
        model = pickle.load(f)
    
    data = pd.DataFrame([[jam, jumlah_antrian, estimasi_menit]], 
                     columns=['jam_order', 'jumlah_antrian', 'estimasi_menit'])
    hasil_klasifikasi = model.predict(data)[0]
    prediksi = "Telat" if hasil_klasifikasi == 1 else "Tepat"
    
    if mode == 'simple':
        return prediksi
    
    # [2] Clustering: kategori jam
    kategori_jam, avg_estimasi_jam = get_jam_kategori(jam)
    
    # Estimasi dinamis berdasarkan kategori jam
    base_estimasi = estimasi_menit
    if kategori_jam == 'sibuk':
        final_estimasi = round(base_estimasi * 1.3)
    elif kategori_jam == 'sepi':
        final_estimasi = round(base_estimasi * 0.85)
    else:
        final_estimasi = round(base_estimasi * 1.0)
    
    result = {
        'prediksi': prediksi,
        'kategori_jam': kategori_jam,
        'estimasi_menit': final_estimasi,
    }
    
    return json.dumps(result)

if __name__ == "__main__":
    jam = sys.argv[1]
    antrian = int(sys.argv[2])
    estimasi = int(sys.argv[3])
    items = sys.argv[4] if len(sys.argv) > 4 else ''
    mode = sys.argv[5] if len(sys.argv) > 5 else 'full'

    hasil = predict(jam, antrian, estimasi, items, mode)
    print(hasil)
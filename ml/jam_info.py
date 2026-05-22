
import pickle
import os
import json

def main():
    base_dir = os.path.dirname(os.path.abspath(__file__))
    cluster_path = os.path.join(base_dir, 'cluster_model.pkl')

    if not os.path.exists(cluster_path):
        print(json.dumps({'error': 'cluster_model.pkl belum dibuat'}))
        return

    with open(cluster_path, 'rb') as f:
        cluster_data = pickle.load(f)

    jam_map = cluster_data['jam_map']
    result = []

    for jam in sorted(jam_map.keys()):
        info = jam_map[jam]
        result.append({
            'jam': f'{jam:02d}:00',
            'kategori': info['kategori'],
            'avg_estimasi': info['avg_estimasi'],
            'avg_antrian': info['avg_antrian'],
        })

    print(json.dumps(result))

if __name__ == '__main__':
    main()
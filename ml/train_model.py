import pandas as pd
import numpy as np
import pickle
import os
from sklearn.tree import DecisionTreeClassifier
from sklearn.cluster import KMeans
from sklearn.preprocessing import StandardScaler
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score, classification_report


# 1. GENERATE DATA SIMULASI


np.random.seed(42)
n = 500

jam_bulat = np.random.randint(8, 22, n)
menit = np.random.randint(0, 60, n)
jam_order = np.round(jam_bulat + menit / 60, 2)
jumlah_antrian = np.random.randint(1, 20, n)
estimasi_menit = np.random.choice([20, 30, 45, 60], n)

# Item menu dimsum yang sering dipesan
MENU_ITEMS = [
    'nasgor_kebuli_sapi', 'nasgor_kebuli_ayam', 'nasi_mie_geprek',
    'nasi_mie_ayam_bakar', 'nasi_ayam_geprek', 'nasi_ayam_penyet',
    'nasi_lele_penyet', 'nasi_sfc', 'kentang_goreng', 'nasi_soto_ayam',
    'nasi_soto_babat', 'nasi_soto_sapi', 'es_teh_manis', 'jeruk_es',
    'tahu_tempe_goreng', 'kerupuk_udang'
]

def generate_order_items(jam):
    if 11 <= jam <= 13 or 17 <= jam <= 19:
        k = np.random.randint(2, 5)
    else:
        k = np.random.randint(1, 3)
    chosen = np.random.choice(MENU_ITEMS, size=k, replace=False)
    return list(chosen)

order_items_list = [generate_order_items(j) for j in jam_order]

def buat_label(jam, antrian, estimasi):
    skor = 0
    if 11 <= jam <= 13 or 17 <= jam <= 19:
        skor += 2
    if antrian >= 8:
        skor += 2
    if estimasi >= 45:
        skor += 1
    noise = np.random.randint(0, 3)
    return 1 if (skor + noise) >= 3 else 0

labels = [buat_label(jam_order[i], jumlah_antrian[i], estimasi_menit[i]) for i in range(n)]

df = pd.DataFrame({
    'jam_order': jam_order,
    'jumlah_antrian': jumlah_antrian,
    'estimasi_menit': estimasi_menit,
    'order_items': [','.join(items) for items in order_items_list],
    'label': labels
})

os.makedirs('ml', exist_ok=True)
df.to_csv('ml/dataset.csv', index=False)
print(f"[1] Dataset berhasil dibuat: {len(df)} baris")
print(f"    Distribusi label: Tepat={sum(l==0 for l in labels)}, Telat={sum(l==1 for l in labels)}")
print()


# 2. KLASIFIKASI - Decision Tree (Prediksi Telat/Tepat)


X = df[['jam_order', 'jumlah_antrian', 'estimasi_menit']]
y = df['label']

X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

clf = DecisionTreeClassifier(max_depth=5, random_state=42)
clf.fit(X_train, y_train)

y_pred = clf.predict(X_test)
akurasi = accuracy_score(y_test, y_pred)
print(f"[2] KLASIFIKASI - Decision Tree")
print(f"    Akurasi: {akurasi * 100:.2f}%")
print(f"    {classification_report(y_test, y_pred, target_names=['Tepat', 'Telat'])}")

with open('ml/model.pkl', 'wb') as f:
    pickle.dump(clf, f)
print("    Model disimpan: ml/model.pkl")
print()


# 3. CLUSTERING - K-Means Jam Sibuk

# Fitur: jam_order + rata-rata antrian per jam + rata-rata durasi per jam

hourly_stats = df.groupby(df['jam_order'].astype(int)).agg(
    avg_antrian=('jumlah_antrian', 'mean'),
    avg_estimasi=('estimasi_menit', 'mean'),
    jumlah_order=('jam_order', 'count')
).reset_index()
hourly_stats.columns = ['jam', 'avg_antrian', 'avg_estimasi', 'jumlah_order']

scaler = StandardScaler()
X_cluster = scaler.fit_transform(hourly_stats[['avg_antrian', 'avg_estimasi', 'jumlah_order']])

kmeans = KMeans(n_clusters=3, random_state=42, n_init=10)
hourly_stats['cluster'] = kmeans.fit_predict(X_cluster)

# Label cluster berdasarkan rata-rata antrian (tinggi = sibuk)
cluster_means = hourly_stats.groupby('cluster')['avg_antrian'].mean().sort_values()
cluster_label_map = {
    cluster_means.index[0]: 'sepi',
    cluster_means.index[1]: 'normal',
    cluster_means.index[2]: 'sibuk'
}
hourly_stats['kategori'] = hourly_stats['cluster'].map(cluster_label_map)

# Simpan mapping jam -> kategori + estimasi
jam_cluster_map = {}
for _, row in hourly_stats.iterrows():
    jam_cluster_map[int(row['jam'])] = {
        'kategori': row['kategori'],
        'avg_estimasi': round(row['avg_estimasi'], 1),
        'avg_antrian': round(row['avg_antrian'], 1)
    }

cluster_data = {
    'kmeans': kmeans,
    'scaler': scaler,
    'jam_map': jam_cluster_map,
    'hourly_stats': hourly_stats.to_dict('records')
}

with open('ml/cluster_model.pkl', 'wb') as f:
    pickle.dump(cluster_data, f)

print(f"[3] CLUSTERING - K-Means Jam Sibuk")
for jam, info in sorted(jam_cluster_map.items()):
    print(f"    Jam {jam:02d}:00 -> {info['kategori'].upper()} (avg antrian: {info['avg_antrian']}, avg estimasi: {info['avg_estimasi']} menit)")
print("    Model disimpan: ml/cluster_model.pkl")
print()

print("SELESAI. Semua model berhasil dibuat.")
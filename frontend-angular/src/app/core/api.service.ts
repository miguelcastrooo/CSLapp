import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, shareReplay, timeout } from 'rxjs';

export interface Nivel { id: number; nombre: string; }
export interface Grado { id: number; nombre: string; orden_promocion: number; }
export interface Seccion { id: number; nombre: string; }
export interface Plataforma { id: number; nombre: string; }
export interface Alumno { id: number; matricula: string; nombre: string; apellidopaterno: string; apellidomaterno: string; seccion: string | null; }
export interface Calificacion { id: number; alumno_id: number; plataforma_id: number; periodo: string; calificacion: string; plataforma: Plataforma; }

@Injectable({ providedIn: 'root' })
export class ApiService {
  // Laravel se ejecuta con `php artisan serve` en el puerto 8000 durante desarrollo.
  private readonly baseUrl = 'http://127.0.0.1:8000/api/v1';
  private nivelesCache?: Observable<Nivel[]>;
  private readonly gradosCache = new Map<number, Observable<Grado[]>>();
  private readonly plataformasCache = new Map<number, Observable<Plataforma[]>>();
  private readonly seccionesCache = new Map<number, Observable<Seccion[]>>();
  private readonly alumnosCache = new Map<string, Observable<Alumno[]>>();

  constructor(private readonly http: HttpClient) {}

  login(email: string, password: string) {
    return this.http.post<{ token: string; user: { name: string; roles: string[] } }>(`${this.baseUrl}/auth/login`, { email, password, device_name: 'portal-angular' });
  }
  logout() { return this.http.post<void>(`${this.baseUrl}/auth/logout`, {}); }
  niveles() {
    return this.nivelesCache ??= this.http.get<Nivel[]>(`${this.baseUrl}/niveles`).pipe(shareReplay(1));
  }
  grados(nivelId: number) {
    return this.desdeCache(this.gradosCache, nivelId, () => this.http.get<Grado[]>(`${this.baseUrl}/niveles/${nivelId}/grados`));
  }
  plataformas(nivelId: number) {
    return this.desdeCache(this.plataformasCache, nivelId, () => this.http.get<Plataforma[]>(`${this.baseUrl}/niveles/${nivelId}/plataformas`));
  }
  secciones(gradoId: number) {
    return this.desdeCache(this.seccionesCache, gradoId, () => this.http.get<Seccion[]>(`${this.baseUrl}/grados/${gradoId}/secciones`));
  }
  alumnos(nivelId: number, gradoId: number, seccion?: string) {
    const key = `${nivelId}:${gradoId}:${seccion ?? ''}`;
    return this.desdeCache(this.alumnosCache, key, () => this.http.get<Alumno[]>(`${this.baseUrl}/alumnos`, { params: { nivel_id: nivelId, grado_id: gradoId, ...(seccion ? { seccion } : {}) } }));
  }
  calificaciones(alumnoId: number, periodo: string) { return this.http.get<Calificacion[]>(`${this.baseUrl}/calificaciones`, { params: { alumno_id: alumnoId, periodo } }); }
  guardarCalificacion(alumnoId: number, plataformaId: number, periodo: string, calificacion: number) {
    return this.http.post<Calificacion>(`${this.baseUrl}/calificaciones`, { alumno_id: alumnoId, plataforma_id: plataformaId, periodo, calificacion }).pipe(timeout(15000));
  }

  private desdeCache<T>(cache: Map<number | string, Observable<T>>, key: number | string, request: () => Observable<T>): Observable<T> {
    const existing = cache.get(key);
    if (existing) return existing;
    const cached = request().pipe(shareReplay(1));
    cache.set(key, cached);
    return cached;
  }
}

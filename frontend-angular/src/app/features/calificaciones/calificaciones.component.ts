import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { ApiService, Alumno, Calificacion, Grado, Nivel, Plataforma, Seccion } from '../../core/api.service';

@Component({ selector: 'app-calificaciones', imports: [FormsModule], templateUrl: './calificaciones.component.html', styleUrl: './calificaciones.component.scss' })
export class CalificacionesComponent implements OnInit {
  niveles: Nivel[] = []; grados: Grado[] = []; secciones: Seccion[] = []; plataformas: Plataforma[] = []; alumnos: Alumno[] = []; calificaciones: Calificacion[] = [];
  nivelId?: number; gradoId?: number; seccion = ''; alumnoId?: number; plataformaId?: number; periodo = '2026-2027-1'; nuevaCalificacion?: number;
  loading = false; saving = false; message = ''; error = '';
  constructor(private readonly api: ApiService, private readonly router: Router) {}
  ngOnInit(): void { this.cargarNiveles(); }
  get alumnoSeleccionado(): Alumno | undefined { return this.alumnos.find((alumno) => alumno.id === this.alumnoId); }
  cargarNiveles(): void { this.loading = true; this.api.niveles().subscribe({ next: (niveles) => { this.niveles = niveles; this.loading = false; }, error: () => this.sesionInvalida() }); }
  cambioNivel(): void { this.gradoId = undefined; this.seccion = ''; this.alumnoId = undefined; this.plataformaId = undefined; this.grados = []; this.secciones = []; this.alumnos = []; this.calificaciones = []; this.plataformas = []; if (!this.nivelId) return; this.api.grados(this.nivelId).subscribe({ next: (grados) => this.grados = grados, error: () => this.mostrarError('No se pudieron cargar los grados.') }); this.api.plataformas(this.nivelId).subscribe({ next: (plataformas) => this.plataformas = plataformas, error: () => this.mostrarError('No se pudieron cargar las plataformas.') }); }
  cambioGrado(): void { this.seccion = ''; this.alumnoId = undefined; this.secciones = []; this.alumnos = []; this.calificaciones = []; if (!this.gradoId) return; this.api.secciones(this.gradoId).subscribe({ next: (secciones) => this.secciones = secciones, error: () => this.mostrarError('No se pudieron cargar las secciones.') }); this.buscarAlumnos(); }
  buscarAlumnos(): void { this.alumnoId = undefined; this.calificaciones = []; if (!this.nivelId || !this.gradoId) return; this.loading = true; this.api.alumnos(this.nivelId, this.gradoId, this.seccion).subscribe({ next: (alumnos) => { this.alumnos = alumnos; this.loading = false; }, error: () => { this.loading = false; this.mostrarError('No se pudieron cargar los alumnos.'); } }); }
  cargarCalificaciones(): void { this.calificaciones = []; this.message = ''; if (!this.alumnoId) return; this.api.calificaciones(this.alumnoId, this.periodo).subscribe({ next: (calificaciones) => this.calificaciones = calificaciones, error: () => this.mostrarError('No se pudieron consultar las calificaciones.') }); }
  guardar(): void { if (!this.alumnoId || !this.plataformaId || this.nuevaCalificacion === undefined || this.saving) { this.mostrarError('Selecciona un alumno, plataforma y calificación.'); return; } this.saving = true; this.error = ''; this.message = ''; this.api.guardarCalificacion(this.alumnoId, this.plataformaId, this.periodo, this.nuevaCalificacion).subscribe({ next: () => { this.saving = false; this.message = 'Calificación guardada correctamente.'; this.nuevaCalificacion = undefined; this.cargarCalificaciones(); }, error: (response) => { this.saving = false; this.mostrarError(response.name === 'TimeoutError' ? 'El servidor tardó demasiado. Intenta de nuevo.' : response.status === 403 ? 'Tu perfil sólo puede consultar calificaciones.' : 'No fue posible guardar la calificación.'); } }); }
  cerrarSesion(): void { this.api.logout().subscribe({ complete: () => this.sesionInvalida(), error: () => this.sesionInvalida() }); }
  private sesionInvalida(): void { sessionStorage.removeItem('csl_api_token'); this.router.navigateByUrl('/login'); }
  private mostrarError(message: string): void { this.error = message; this.message = ''; }
}

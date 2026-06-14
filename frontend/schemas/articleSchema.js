import { z } from 'zod';

export const articleSchema = z.object({
  title: z
    .string()
    .min(1, 'Le titre est obligatoire')
    .max(255, 'Le titre ne doit pas dépasser 255 caractères'),
  content: z.string().min(1, 'Le contenu est obligatoire'),
  status: z.enum(['draft', 'published']),
});
-- Add AI classification fields to reclamation table
ALTER TABLE reclamation 
ADD COLUMN IF NOT EXISTS category VARCHAR(50) DEFAULT 'general',
ADD COLUMN IF NOT EXISTS department VARCHAR(100) DEFAULT 'General Support';

-- Add index for faster filtering
CREATE INDEX IF NOT EXISTS idx_reclamation_category ON reclamation(category);
CREATE INDEX IF NOT EXISTS idx_reclamation_priority ON reclamation(priorite);
